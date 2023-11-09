<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;


class ReportPage extends Component
{
    public $startDate;
    public $endDate;
    public $displayDate;
    public $integrationId = 124;
    public $nameFilter;
    public $countryFilter;
    public $data;
    public $date;
    public $productFilter;
    public $operatorFilter;
    public $aggregatorFilter;

    public $totalRenewals = 0;
    public $totalUnsubs = 0;
    public $totalAmount;
    public $our_share;
    public $currency_conversion;
    public $totalSubs = 0;
    public $totalPaid = 0;
    public $totalCharged = 0;
    public $selectedItem;

    public function mount()
    {
        $this->startDate = session('start_date', date('Ymd', strtotime('2023-10-01')));
        $this->endDate = session('end_date', date('Ymd', strtotime('2023-10-15')));
    }

    public function view($integrationId)
    {

        $document = DB::connection('mongodb')->collection('all_reports_daily')
            ->where('integration_id', (int)'61')
            ->first();

        $document['_id'] = (string)$document['_id'];

        $this->selectedItem = $document;
    }


    public function render()
    {

        $this->displayDate = isset($this->displayDate) ? date('Ymd', strtotime($this->displayDate)) : date('Ymd');

        $mongodb = DB::connection('mongodb');

        $query = $mongodb->collection('all_reports_daily')->where('date', (int)$this->displayDate);

        if ($this->nameFilter) {
            $query->where('integration_data.name', 'like', '%' . $this->nameFilter . '%');
        }

        if ($this->countryFilter) {
            $query->where('integration_data.country', '=', $this->countryFilter);
        }

        if ($this->productFilter) {
            $query->where('integration_data.product', 'like', '%' . $this->productFilter . '%');
        }

        if ($this->operatorFilter) {
            $query->where('integration_data.operator', 'like', '%' . $this->operatorFilter . '%');
        }

        if ($this->aggregatorFilter) {
            $query->where('integration_data.aggregator', 'like', '%' . $this->aggregatorFilter . '%');
        }

        $matchingDocuments = [];

        foreach ($query->get() as $document) {
            $stats = $document['stats'];
            $areAllDatesMatching = true;

            foreach ($stats as $date => $data) {
                $date = date('Ymd', strtotime($date));

                if (!($date >= $this->startDate && $date <= $this->endDate)) {
                    $areAllDatesMatching = false;
                    break;
                }
            }

            if ($areAllDatesMatching) {
                $matchingDocuments[] = $document;
            }
        }

        $this->data = json_decode(json_encode($matchingDocuments), true);

        $this->calculateTotals();
        $this->calculateTotalAmount();

        $uniqueCountries = $this->getUniqueItems('integration_data.country');
        $uniqueProducts = $this->getUniqueItems('integration_data.product');
        $uniqueOperators = $this->getUniqueItems('integration_data.operator');
        $uniqueAggregators = $this->getUniqueItems('integration_data.aggregator');
        if ($this->selectedItem) {
            $this->calculateTotalsForDetails();

            return view('livewire.report-details', ['totalsByPublisher' => $this->totalsByPublisher,]);
        } else {
            return view('livewire.report-page', [
                'countries' => $uniqueCountries,
                'products' => $uniqueProducts,
                'operators' => $uniqueOperators,
                'aggregators' => $uniqueAggregators,
                'totalAmount' => $this->totalAmount
            ]);
        }
    }

    private function calculateTotals()
    {
        $this->totalRenewals = 0;
        $this->totalUnsubs = 0;
        $this->totalSubs = 0;
        $this->totalPaid = 0;
        $this->totalCharged = 0;

        foreach ($this->data as $id => $item) {
            if (isset($item['stats'])) {


                foreach ($item['stats'] as $dateKey => $dateValue) {
                    $this->data[$id]['stats'][$dateKey]['total_unsubs'] = 0;
                    $this->data[$id]['stats'][$dateKey]['total_subs'] = 0;
                    $this->data[$id]['stats'][$dateKey]['total_charged'] = 0;
                    $this->data[$id]['stats'][$dateKey]['total_paid'] = 0;
                    $this->data[$id]['stats'][$dateKey]['total_renewals'] = 0;
                    $this->data[$id]['stats'][$dateKey]['total_amount'] = 0;

                    foreach ($dateValue as $subValue) {
                        foreach ($subValue as $subsValue) {
                            if (isset($subsValue['subs']['count'])) {
                                $this->data[$id]['stats'][$dateKey]['total_subs'] += $subsValue['subs']['count'];
                            }
                            if (isset($subsValue['renewals']['count'])) {
                                $this->data[$id]['stats'][$dateKey]['total_renewals'] += $subsValue['renewals']['count'];
                                $this->data[$id]['stats'][$dateKey]['total_amount'] += $subsValue['renewals']['amount'];
                            }
                            if (isset($subsValue['paid']['count'])) {
                                $this->data[$id]['stats'][$dateKey]['total_paid'] += $subsValue['paid']['count'];
                            }
                            if (isset($subsValue['charged']['count'])) {
                                $this->data[$id]['stats'][$dateKey]['total_charged'] += $subsValue['charged']['count'];
                                $this->data[$id]['stats'][$dateKey]['total_amount'] += $subsValue['charged']['amount'];
                            }
                            if (isset($subsValue['unsub']['count'])) {

                                $this->data[$id]['stats'][$dateKey]['total_unsubs'] += $subsValue['unsub']['count'];
                            }

                        }
                    }
                    $this->data[$id]['stats'][$dateKey]['total_amount'] *= $item['integration_data']['currency_conversion'] * $item['integration_data']['our_share'];
                }
            }
        }
    }

    private function calculateTotalAmount()
    {
        $mongodb = DB::connection('mongodb');

        $integrationData = $mongodb->collection('all_reports_daily')
            ->where('date', '>=', (int)$this->startDate)
            ->where('date', '<=', (int)$this->endDate)
            ->first();

        if ($integrationData) {
            $ourShare = $integrationData['integration_data']['our_share'];
            $currencyConversion = $integrationData['integration_data']['currency_conversion'];

            $this->totalAmount = ($this->totalRenewals + $this->totalSubs) * $ourShare * $currencyConversion;
        }
    }

    private function getUniqueItems($field)
    {
        $mongodb = DB::connection('mongodb');
        $uniqueItems = $mongodb->collection('all_reports_daily')
            ->distinct($field)
            ->get();

        return $uniqueItems;
    }

    private function calculateTotalsForDetails()
    {
        if ($this->selectedItem) {
            $this->totalsByPublisher = [];

            $documentMonth = substr($this->selectedItem['date'], 0, 6); // Extract the year and month from the document's date

            foreach ($this->selectedItem['stats'] as $dateKey => $dateValue) {
                // Check if the date is from the document's month
                if (substr($dateKey, 0, 6) !== $documentMonth) {
                    continue; // Skip data from other months
                }

                foreach ($dateValue as $pubkey => $publisherStats) {
                    // Each $publisherStats array represents a publisher
                    foreach ($publisherStats as $publisherId => $stats) {

                        // Use the publisher's ID as the identifier


                        if (!isset($this->totalsByPublisher[$publisherId])) {
                            $this->totalsByPublisher[$publisherId] = [
                                'totalRenewals' => 0,
                                'totalUnsubs' => 0,
                                'totalSubs' => 0,
                                'totalPaid' => 0,
                                'totalCharged' => 0,
                                'totalRenewalFailed' => 0,
                                'totalRenewalAmount' => 0,
                                'totalAmount' => 0,

                            ];
                        }

                        $totals = &$this->totalsByPublisher[$publisherId];

                        if (isset($stats['renewals']['count'])) {
                            $totals['totalRenewals'] += $stats['renewals']['count'];
                            $totals['totalRenewalAmount'] += $stats['renewals']['amount'];
                        }

                        if (isset($stats['unsub']['count'])) {
                            $totals['totalUnsubs'] += $stats['unsub']['count'];
                        }

                        if (isset($stats['subs']['count'])) {
                            $totals['totalSubs'] += $stats['subs']['count'];
                        }

                        if (isset($stats['charged']['count'])) {
                            $totals['totalCharged'] += $stats['charged']['count'];
                            $totals['totalAmount'] += $stats['charged']['amount'];
                        }

                        if (isset($stats['renewal_failed']['count'])) {
                            $totals['totalRenewalFailed'] += $stats['renewal_failed']['count'];
                        }

                        if (isset($stats['paid']['count'])) {
                            $totals['totalPaid'] += $stats['paid']['count'];
                        }

// Calculate totals for sub-publishers within the publisher's stats
                        foreach ($dateValue[0] as $publisherStats) {
                            foreach ($publisherStats as $subPublisherData) {


                                $subPublisherId = key($publisherStats);

                                $subStats = current($subPublisherData);

                                if (!isset($this->totalsByPublisher[$publisherId][$subPublisherId])) {
                                    $this->totalsByPublisher[$publisherId][$subPublisherId] = [
                                        'totalRenewals' => 0,
                                        'totalUnsubs' => 0,
                                        'totalSubs' => 0,
                                        'totalPaid' => 0,
                                        'totalCharged' => 0,
                                        'totalRenewalFailed' => 0,
                                        'totalRenewalAmount' => 0,
                                    ];
                                }

                                $subTotals = &$this->totalsByPublisher[$publisherId][$subPublisherId];

                                if (isset($subStats['renewals']['count'])) {
                                    $subTotals['totalRenewals'] += $subStats['renewals']['count'];
                                    $subTotals['totalRenewalAmount'] += $subStats['renewals']['amount'];
                                }

                                if (isset($subStats['unsub']['count'])) {
                                    $subTotals['totalUnsubs'] += $subStats['unsub']['count'];
                                }

                                if (isset($subStats['subs']['count'])) {
                                    $subTotals['totalSubs'] += $subStats['subs']['count'];
                                }

                                if (isset($subStats['charged']['count'])) {
                                    $subTotals['totalCharged'] += $subStats['charged']['count'];
                                    $subTotals['totalAmount'] += $subStats['charged']['amount'];
                                }

                                if (isset($subStats['renewal_failed']['count'])) {
                                    $subTotals['totalRenewalFailed'] += $subStats['renewal_failed']['count'];
                                }

                                if (isset($subStats['paid']['count'])) {
                                    $subTotals['totalPaid'] += $subStats['paid']['count'];
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}


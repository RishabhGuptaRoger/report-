<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;


class ReportPage extends Component
{
    public $startDate;
    public $endDate;
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

    public function render()
    {

        $this->startDate = isset($this->startDate) ? date('Ymd', strtotime($this->startDate)) : date('Ymd');

        $this->endDate = isset($this->endDate) ? date('Ymd', strtotime($this->endDate)) : date('Ymd');

        $mongodb = DB::connection('mongodb');

        $query = $mongodb->collection('all_reports_daily')
            ->where('date', '>=', (int)$this->startDate)
            ->where('date', '<=', (int)$this->endDate);
//            ->where('integration_id', 124);

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


        $this->data = $query->get();

        $this->data = json_decode(json_encode($this->data), true);

        $this->calculateTotals();

        $uniqueCountries = $this->getUniqueItems('integration_data.country');
        $uniqueProducts = $this->getUniqueItems('integration_data.product');
        $uniqueOperators = $this->getUniqueItems('integration_data.operator');
        $uniqueAggregators = $this->getUniqueItems('integration_data.aggregator');
        return view('livewire.report-page', [
            'countries' => $uniqueCountries,
            'products' => $uniqueProducts,
            'operators' => $uniqueOperators,
            'aggregators' => $uniqueAggregators,
        ]);
    }

    private function calculateTotals()
    {
        $this->totalRenewals = 0;
        $this->totalUnsubs = 0;

        foreach ($this->data as $item) {
            if (isset($item['stats'])) {
                foreach ($item['stats'] as $dateKey => $dateValue) {
                    foreach ($dateValue as $subValue) {
                        foreach ($subValue as $subsValue) {
                            if (isset($subsValue['renewals']['count'])) {
                                $this->totalRenewals += $subsValue['renewals']['count'];
                            }
                            if (isset($subsValue['unsub']['count'])) {
                                $this->totalUnsubs += $subsValue['unsub']['count'];
                            }
                        }
                    }
                }
            }
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
}


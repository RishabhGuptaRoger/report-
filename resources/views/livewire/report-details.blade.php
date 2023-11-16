<div class="container">
    <h2>{{ $selectedItem['integration_data']['name'] }} Publisher Report</h2>

    <!-- Publisher Report Table -->
    <div class="table-responsive">
        <h1>Publisher Report Details</h1>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Publisher ID</th>
                <th>Total Renewals</th>
                <th>Total Unsubs</th>
                <th>Total Subs</th>
                <th>Total Paid</th>
                <th>Total Charged</th>
                <th>Total Renewal Failed</th>
            </tr>
            </thead>
            <tbody>
            @foreach($totalsByPublisher as $publisherId => $totals)
                <tr>
                    <td>{{ $publisherId }}</td>
                    <td>{{ $totals['totalRenewals'] }}</td>
                    <td>{{ $totals['totalUnsubs'] }}</td>
                    <td>{{ $totals['totalSubs'] }}</td>
                    <td>{{ $totals['totalPaid'] }}</td>
                    <td>{{ $totals['totalCharged'] }}</td>
                    <td>{{ $totals['totalRenewalFailed'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Sub-Publisher Report Table -->
    <div class="table-responsive">
        <h1>Sub-Publisher Report Details</h1>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Publisher ID</th>
                <th>Sub-Publisher ID</th>
                <th>Total Renewals</th>
                <th>Total Unsubs</th>
                <th>Total Subs</th>
                <th>Total Paid</th>
                <th>Total Charged</th>
                <th>Total Renewal Failed</th>
            </tr>
            </thead>
            <tbody>
            @foreach($totalsByPublisher as $publisherId => $publisherTotals)
                @if (isset($publisherTotals['subPublishers']) && count($publisherTotals['subPublishers']))
                    @foreach($publisherTotals['subPublishers'] as $subPublisherId => $subTotals)
                        <tr>
                            <td>{{ $publisherId }}</td>
                            <td>{{ $subPublisherId }}</td>
                            <td>{{ $subTotals['totalRenewals'] }}</td>
                            <td>{{ $subTotals['totalUnsubs'] }}</td>
                            <td>{{ $subTotals['totalSubs'] }}</td>
                            <td>{{ $subTotals['totalPaid'] }}</td>
                            <td>{{ $subTotals['totalCharged'] }}</td>
                            <td>{{ $subTotals['totalRenewalFailed'] }}</td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
</div>

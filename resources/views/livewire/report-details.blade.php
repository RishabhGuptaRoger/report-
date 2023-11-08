<div class="container">

        <h2>{{ $selectedItem['integration_data']['name'] }} Publisher Report</h2>

        <div class="table-responsive">
            <h1>Report Details</h1>

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
{{--                    <th>Total Renewal Amount</th>--}}
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
{{--                        <td>{{ $totals['totalRenewalAmount'] }}</td>--}}
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

</div>

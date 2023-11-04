<div>
    <div class="container">
        <h1>Integrations Report</h1>

        <div class="row mb-3">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="startDate">Start Date:</label>
                    <input type="date" id="startDate" wire:model="startDate" class="form-control">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="endDate">End Date:</label>
                    <input type="date" id="endDate" wire:model="endDate" class="form-control">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="nameFilter">Name Filter:</label>
                    <input type="text" id="nameFilter" wire:model="nameFilter" class="form-control">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="countryFilter">Country Filter:</label>
                    <select id="countryFilter" wire:model="countryFilter" class="form-control">
                        <option value="">All Countries</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country }}">{{ $country }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="productFilter">Product Filter:</label>
                    <select id="productFilter" wire:model="productFilter" class="form-control">
                        <option value="">All Products</option>
                        @foreach ($products as $product)
                            <option value="{{ $product }}">{{ $product }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="operatorFilter">Operator Filter:</label>
                    <select id="operatorFilter" wire:model="operatorFilter" class="form-control">
                        <option value="">All Operators</option>
                        @foreach ($operators as $operator)
                            <option value="{{ $operator }}">{{ $operator }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="aggregatorFilter">Aggregator Filter:</label>
                    <select id="aggregatorFilter" wire:model="aggregatorFilter" class="form-control">
                        <option value="">All Aggregators</option>
                        @foreach ($aggregators as $aggregator)
                            <option value="{{ $aggregator }}">{{ $aggregator }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button wire:click="$refresh" class="btn btn-primary form-control">Apply Filter</button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Our Share</th>
                    <th>Country</th>
                    <th>Product</th>
                    <th>Operator</th>
                    <th>Aggregator</th>
                    <th>Currency Conversion</th>
                    <th>Renewals</th>
                    <th>Unsubs</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $item['integration_data']['name'] }}</td>
                        <td>{{ $item['integration_data']['our_share'] }}</td>
                        <td>{{ $item['integration_data']['country'] }}</td>
                        <td>{{ $item['integration_data']['product'] }}</td>
                        <td>{{ $item['integration_data']['operator'] }}</td>
                        <td>{{ $item['integration_data']['aggregator'] }}</td>
                        <td>{{ $item['integration_data']['currency_conversion'] }}</td>
                        <td>{{ $totalRenewals}}</td>
                        <td>{{ $totalUnsubs }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

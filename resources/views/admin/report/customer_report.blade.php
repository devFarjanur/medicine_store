@extends('admin.admin_dashboard')

@section('admin')
<div class="page-content">
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.report') }}">Report</a></li>
            <li class="breadcrumb-item active" aria-current="page">Customer Report</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <!-- Search Form -->
                    <form action="{{ route('customer.report') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="customer_name" class="form-label">Customer Name</label>
                                <input type="text" name="customer_name" class="form-control"
                                    placeholder="Search by customer name" value="{{ request('customer_name') }}">
                            </div>

                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input class="form-control" type="date" name="start_date"
                                    value="{{ old('start_date', $startDate) }}">
                            </div>

                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input class="form-control" type="date" name="end_date"
                                    value="{{ old('end_date', $endDate) }}">
                            </div>

                            <div class="col-md-2 pt-4">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <!-- Reset Button -->
                                <a href="{{ route('customer.report') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    @if($customers->count() > 0)
                        <div class="table-responsive">
                            <table id="dataTableExample" class="table table-hover text-center">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Customer Name</th>
                                        <th class="text-center">Total Orders</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $customer)
                                        @if ($customer->orders_count > 0)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $customer->firstname }} {{ $customer->lastname }}</td>
                                                <td>{{ $customer->orders_count }}</td>
                                                <td>
                                                    <a href="{{ route('customer.report.all.orders', $customer->id) }}"
                                                        class="btn btn-primary">View Orders</a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center">No orders found for the selected date range.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<div class="card card-info mt-2">
    <div class="card-body">
        <div class="row clearfix">
            <div class="col-md-12">
                <div class="card border-0 mb-4 no-bg">
                    <div
                        class="card-header py-3 px-0 d-sm-flex align-items-center bg-light text-center  justify-content-between border-bottom">
                        <h3 class=" fw-bold flex-fill mb-0 mt-sm-0">Project Interaction </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <table class="table">
                                            <thead class="bg-light">
                                                <th>Date Time</th>
                                                <th>Description</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($logs as $interaction)
                                                    <tr>
                                                        <td>{{ $interaction->created_at }}</td>
                                                        <td>{{ $interaction->description ?? 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@extends('organizer-app')

@section('content')
<div class="ticket-details-page">
    <div class="container">
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">

                    <!-- Event Card Component -->
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4 mt-4">
                        <div class="event-card bg-light shadow-sm">
                            <div class="row align-items-center">
                                <div class="col-4 text-center">
                                    <img src="https://vignette.wikia.nocookie.net/nationstates/images/2/29/WS_Logo.png/revision/latest?cb=20080507063620" class="img-fluid" alt="Icon">
                                </div>
                                <div class="col-8">
                                    <h5 class="text-secondary">Total Events</h5>
                                    <h3 class="text-primary">{{ $myTotalEvents }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4 mt-4">
                        <div class="event-card bg-light shadow-sm">
                            <div class="row align-items-center">
                                <div class="col-4 text-center">
                                    <img src="https://vignette.wikia.nocookie.net/nationstates/images/2/29/WS_Logo.png/revision/latest?cb=20080507063620" class="img-fluid" alt="Icon">
                                </div>
                                <div class="col-8">
                                    <h5 class="text-secondary">Active Events</h5>
                                    <h3 class="text-success">{{ $myActiveEvents }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4 mt-4">
                        <div class="event-card bg-light shadow-sm">
                            <div class="row align-items-center">
                                <div class="col-4 text-center">
                                    <img src="https://vignette.wikia.nocookie.net/nationstates/images/2/29/WS_Logo.png/revision/latest?cb=20080507063620" class="img-fluid" alt="Icon">
                                </div>
                                <div class="col-8">
                                    <h5 class="text-secondary">Inactive Events</h5>
                                    <h3 class="text-warning">{{ $myInActiveEvents }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4 mt-4">
                        <div class="event-card bg-light shadow-sm">
                            <div class="row align-items-center">
                                <div class="col-4 text-center">
                                    <img src="https://vignette.wikia.nocookie.net/nationstates/images/2/29/WS_Logo.png/revision/latest?cb=20080507063620" class="img-fluid" alt="Icon">
                                </div>
                                <div class="col-8">
                                    <h5 class="text-secondary">Cancelled Events</h5>
                                    <h3 class="text-danger">{{ $myCancelledEvents }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

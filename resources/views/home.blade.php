@extends('app')

@section('content')

<div class="page-heading-shows-events">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2>Our Shows & Events</h2>
                <span>Check out upcoming and past shows & events.</span>
            </div>
        </div>
    </div>
</div>

<div class="tickets-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="search-box">
                    <form id="filter-form">
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="search-heading">
                                    <h4>Sort The Upcoming Shows & Events By:</h4>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <fieldset>
                                            <input class="" name="keyword" type="text" id="filterKeyword" placeholder="Enter keyword" >
                                        </fieldset>
                                    </div>
                                    <div class="col-lg-3">
                                        <fieldset>
                                            <select name="city_id" id="filterCityId">
                                                <option value="">Location</option>
                                                @foreach ($cities as $city)
                                                    <option value="{{$city->id}}" {{ (auth()->check() && auth()->user()->city && auth()->user()->city->id == $city->id) ||
                                                        (session('guest_city_id') == $city->id) ? 'selected' : '' }}>
                                                        {{ $city->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-lg-3">
                                        <input name="date" type="date" id="filterDate" >
                                    </div>
                                    <div class="col-lg-3">
                                        <fieldset>
                                            <button type="submit" id="filter-submit"
                                                class="main-dark-button">Submit</button>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div>
            <div class="loader" style="display: none; text-align: center;margin-top: 50px">
                <img src="{{ asset('assets/images/loader.gif') }}" alt="loader">
            </div>
        </div>

        <div class="row" id="events-list" bis_skin_checked="1">

        </div>
    </div>
</div>
@endsection

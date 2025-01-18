<?php

namespace App\Http\Controllers;

use AkkiIo\LaravelGoogleAnalytics\Facades\LaravelGoogleAnalytics;
use AkkiIo\LaravelGoogleAnalytics\Period;
use Illuminate\Http\Request;

class GoogleAnalyticsController extends Controller
{
    public function visitorsAndPageViews()
    {
        //retrieve visitors and page view data for the current day and the last seven days
        $analyticsData = LaravelGoogleAnalytics::getTotalViewsByPage(Period::days(7));
        return json_encode($analyticsData);
    }
}

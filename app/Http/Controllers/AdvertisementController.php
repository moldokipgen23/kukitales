<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;

class AdvertisementController extends Controller
{
    public function click(Advertisement $advertisement)
    {
        $advertisement->increment('click_count');
        $target = $advertisement->link_url ?: url('/');
        return redirect()->away($target);
    }
}

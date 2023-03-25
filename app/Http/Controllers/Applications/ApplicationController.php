<?php

namespace App\Http\Controllers\Applications;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProfileController;
use App\Http\Requests\ApplicationRequest;
use App\Models\Application;
use App\Models\TableType;
use App\Notifications\CanceledByDealershipNotification;
use App\Notifications\CanceledBySelfNotification;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    public function create(Request $request)
    {
        $application = \Auth::user()->application ?? new Application();
        $applicationType = Application::determineApplicationTypeByCode($request->get('code'));
        return view('application.create',[
            'table_types' => TableType::all(['id','name','price']),
            'application' => $application,
            'applicationType' => $applicationType,
            'code' => $request->get('code'),
            'profile' => ProfileController::getOrCreate($application->id)
        ]);
    }

    public function store(ApplicationRequest $request)
    {
        $request->act();
        \Auth::user()->notify(new WelcomeNotification());
        return \Redirect::route('dashboard')->with('save-successful');
    }

    public function edit(Request $request)
    {
        $application = \Auth::user()->application;
        abort_if(is_null($application),403,'No Registration');
        $applicationType = ($request->get('code')) ? Application::determineApplicationTypeByCode($request->get('code')) : $application->type;
        return view('application.edit',[
            'table_types' => TableType::all(['id','name','price']),
            "application" => $application,
            'applicationType' => $applicationType,
            'code' => $request->get('code'),
            'profile' => ProfileController::getByApplicationId($application->id)
        ]);
    }
    public function update(ApplicationRequest $request)
    {
        $request->act();
        return \Redirect::route('applications.edit')->with('save-successful');
    }

    public function delete()
    {
        return view('application.delete',[
            "application" => \Auth::user()->application,
        ]);
    }

    public function destroy()
    {
        $application = \Auth::user()->application;
        foreach($application->children()->get() as $child) {
            $child->update([
                'canceled_at' => now(),
                'parent' => null,
                'type' => 'dealer'
            ]);
            $child->user()->first()->notify(new CanceledByDealershipNotification());
        }
        $application->update([
            'canceled_at' => now(),
            'parent' => null,
            'type' => 'dealer'
        ]);
        \Auth::user()->notify(new CanceledBySelfNotification());
        return \Redirect::route('dashboard');
    }

    /**
     * @param Request $request
     * @return ApplicationType
     */
}

<?php

namespace App\Http\Controllers\Applications;

use App\Enums\ApplicationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationRequest;
use App\Http\Requests\ApplicationUpdateRequest;
use App\Models\Application;
use App\Models\TableType;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function create(Request $request)
    {
        $applicationType = $this->getType($request);
        return view('application.create',[
            'table_types' => TableType::all(['id','name']),
            'application' => new Application(),
            'applicationType' => $applicationType,
            'code' => $request->get('code')
        ]);
    }

    public function store(ApplicationRequest $request)
    {
        $request->store();
        return \Redirect::route('dashboard');
    }

    public function edit(Request $request)
    {
        $application = \Auth::user()->applications;
        $applicationType = $this->getType($request);
        abort_if(is_null($application),403,'No Registration');
        return view('application.edit',[
            'table_types' => TableType::all(['id','name']),
            "application" => $application,
            'applicationType' => $applicationType,
            'code' => $request->get('code')
        ]);
    }
    public function update(ApplicationRequest $request)
    {
        $request->update();
        return \Redirect::route('applications.edit')->with('save-successful');
    }

    public function delete()
    {
        return view('application.delete',[
            "application" => \Auth::user()->applications
        ]);
    }

    public function destroy()
    {
        \Auth::user()->applications->cancel();
        return \Redirect::route('dashboard');
    }

    /**
     * @param Request $request
     * @return ApplicationType
     */
    public function getType(Request $request): ApplicationType
    {
        $applicationType = ApplicationType::Dealer;
        if ($request->exists('code')) {
            $application = Application::where('type', ApplicationType::Dealer)
                ->where(function ($q) use ($request) {
                    return $q->where('invite_code_assistants', $request->get('code'))
                        ->orWhere('invite_code_shares', $request->get('code'));
                })->firstOrFail();


            if ($application->invite_code_shares === $request->get('code')) {
                $applicationType = ApplicationType::Share;
            }
            if ($application->invite_code_assistants === $request->get('code')) {
                $applicationType = ApplicationType::Assistant;
            }
        }
        return $applicationType;
    }
}

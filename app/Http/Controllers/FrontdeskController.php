<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Enums\ApplicationType;
use App\Http\Requests\CheckInRequest;
use App\Http\Requests\CheckOutRequest;
use App\Http\Requests\CommentRequest;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Keyword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class FrontdeskController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $this->authorize('view-any', Application::class);

        $search = $request->get('search');
        $type = $request->get('type') ?? 'default';

        if (empty($search)) {
            $categories = null;
            if ($type === 'keyword') {
                $categories = Category::orderBy('name', 'asc')->get();
            }
            return view('frontdesk', [
                'user' => \Auth::user(),
                'search' => null,
                'type' => $type,
                'applications' => null,
                'application' => null,
                'applicant' => null,
                'categories' => $categories,
            ]);
        }

        $searchResult = [];
        switch ($type) {
            case 'name':
                $searchResult = $this->nameSearch($search);
                break;
            case 'keyword':
                $searchResult = $this->keywordSearch($search);
                break;
            case 'default':
            default:
                $searchResult = $this->defaultSearch($search);
        }

        return view('frontdesk', array_merge([
            'user' => \Auth::user(),
            'search' => $search,
            'type' => $type,
            'showAdditional' => $request->has('show_additional'),
            'applications' => null,
            'application' => null,
            'applicant' => null,
        ], $searchResult));
    }

    private function keywordSearch($search)
    {
        $keywordId = '-1';
        $searchString = $search;
        if (str_starts_with($search, 'k::')) {
            $keywordId = intval(substr($search, 3));
            $searchString = Keyword::where('id', $keywordId)?->first()?->name ?? $search;
        }
        $categoryId = '-1';
        if (str_starts_with($search, 'c::')) {
            $categoryId = intval(substr($search, 3));
            $searchString = Category::where('id', $categoryId)?->first()?->name ?? $search;
        }
        $applications = Application::whereHas('profile.keywords', function ($query) use ($search, $keywordId) {
            $query->where('name', 'like', "%$search%")->orWhere('id', '=', $keywordId);
        })->orWhereHas('profile.keywords.category', function ($query) use ($search, $categoryId) {
            $query->where('name', 'like', "%$search%")->orWhere('id', '=', $categoryId);
        })->get();
        return ['applications' => $applications, 'search' => $searchString];
    }

    private function nameSearch($search)
    {
        $applications = Application::where('display_name', 'like', "%$search%")->orWhereHas('user', function ($query) use ($search) {
            $query->where('name', 'like', "%$search%");
        })->get();
        return ['applications' => $applications];
    }

    private function defaultSearch($search)
    {
        // 1. search by user user_id
        // 2. search by user name
        $user = User::where('reg_id', $search)->orWhere('name', 'like', $search)->first();
        $application = $user ? Application::where('user_id', $user->id)->first() : null;

        // 2. search by application table_number
        // 4. search by dealership display_name
        if ($application === null) {
            $tableNumberVariation1 = preg_replace('/^([a-zA-Z]{1,2}[0-9])([0-9]+)$/', '\1/\2', $search);
            $tableNumberVariation2 = preg_replace('/^([a-zA-Z]{1,2})([0-9]+)$/', '\1/\2', $search);
            $application = Application::where('table_number', strtoupper($search))
                ->orWhere('table_number', strtoupper($tableNumberVariation1))
                ->orWhere('table_number', strtoupper($tableNumberVariation2))
                ->orWhere('display_name', 'like', $search)->first();
            $user = $application ? $application->user : null;
        }

        $table = $application ? $application->assignedTable : null;

        $parent = $application && $application->type !== ApplicationType::Dealer ? $application->parent()->first() : null;
        $parentApplicant = $parent ? $parent->user : null;

        $children = $application && $application->type === ApplicationType::Dealer ? $application->children : [];
        $shares = [];
        $assistants = [];
        foreach ($children as $child) {
            if ($child->type === ApplicationType::Share) {
                array_push($shares, $child);
            } elseif ($child->type === ApplicationType::Assistant) {
                array_push($assistants, $child);
            } else {
                Log::warning('Encountered child of unexpected type.', ['parent' => $parent, 'child' => $child]);
            }
        }

        $profile = $application ? $application->profile : null;

        return [
            'application' => $application,
            'applicant' => $user,
            'table' => $table,
            'parent' => $parent,
            'parentApplicant' => $parentApplicant,
            'shares' => $shares,
            'assistants' => $assistants,
            'profile' => $profile,
        ];
    }

    public function comment(CommentRequest $request)
    {
        $this->authorize('create', Comment::class);

        $text = strip_tags($request->get('comment'));
        $application = Application::findOrFail($request->get('application'));

        $author = \Auth::user();
        Comment::create([
            'text' => trim($text),
            'admin_only' => $request->has('admin_only'),
            'user_id' => $author->id,
            'application_id' => $application->id,
        ]);
        return Redirect::route('frontdesk', [
            "search" => $application->user->reg_id,
        ]);
    }

    public function checkIn(CheckInRequest $request)
    {
        $this->authorize('check-in', Application::class);
        $application = Application::findOrFail($request->get('application'));

        abort_if(!$application->checkIn(), 403, 'Application status does not allow check-in.');

        $comment = strip_tags($request->get('ci_comment'));
        $author = \Auth::user();
        Comment::create([
            'text' => trim(join("\n", ['Check-In Performed', $comment])),
            'admin_only' => false,
            'user_id' => $author->id,
            'application_id' => $application->id,
        ]);

        return Redirect::route('frontdesk', [
            "search" => $application->user->reg_id,
        ]);
    }

    public function checkOut(CheckOutRequest $request)
    {
        $this->authorize('check-out', Application::class);
        $application = Application::findOrFail($request->get('application'));

        abort_if(!$application->checkOut(), 403, 'Application status does not allow check-out.');

        $comment = strip_tags($request->get('co_comment'));
        $author = \Auth::user();
        Comment::create([
            'text' => trim(join("\n", ['Check-Out Performed', $comment])),
            'admin_only' => false,
            'user_id' => $author->id,
            'application_id' => $application->id,
        ]);

        return Redirect::route('frontdesk', [
            "search" => $application->user->reg_id,
        ]);
    }
}

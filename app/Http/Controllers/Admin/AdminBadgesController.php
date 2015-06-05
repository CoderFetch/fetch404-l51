<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Fetch404\Core\Models\Badge;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;

class AdminBadgesController extends Controller
{
    //

    protected $badge;

    /**
     * Create a new badges controller instance.
     *
     * @param Badge $badge
     */
    public function __construct(Badge $badge)
    {
        $this->badge = $badge;
    }

    /**
     * Show an index of the badges.
     *
     * @return Response
     */
    public function index()
    {
        return view('core.admin.badges.index', array('badges' => $this->badge->all()));
    }

    /**
     * Show the view for editing a badge.
     *
     * @param Badge $badge
     * @return Response
     */
    public function edit(Badge $badge)
    {
        return view('core.admin.badges.edit', array('badge' => $badge));
    }

    /**
     * Edit a badge.
     *
     * @param Request $request
     * @param Badge $badge
     * @return Response
     */
    public function update(Request $request, Badge $badge)
    {
        $rules = array(
            'milestone_type' => 'required',
            'userType' => 'required'
        );
        $messages = array(
            'milestone_type.required' => 'Please enter a milestone type.',
            'userType.required' => 'You must select the type of user that this badge can be assigned to.'
        );

        $milestone_type = $request->input('milestone_type', null);

        if ($milestone_type != null && ($milestone_type == 'post' || $milestone_type == 'likes'))
        {
            $rules['milestone_requirement'] = 'required';
            $messages['milestone_requirement.required'] = 'Please enter a requirement for this milestone.';
        }

        $this->validate($request, $rules, $messages);

        $badge = $request->route('badge');

        $criteria = $badge->criteria();

        if ($criteria->whereTriggerType($milestone_type)->count() == 0)
        {
            $criteria->create(array(
                'user_type' => $request->input('userType'),
                'trigger_type' => $milestone_type,
                'trigger_value' => $request->input('milestone_requirement', null)
            ));
        }
        else
        {
            $criteriaObj = $criteria->whereTriggerType($milestone_type)->first();

            $criteriaObj->update(array(
                'user_type' => $request->input('userType'),
                'trigger_type' => $milestone_type,
                'trigger_value' => $request->input('milestone_requirement', null)
            ));
        }

        Flash::success('Updated badge');

        return view('core.admin.badges.edit', array('badge' => $badge));
    }
}

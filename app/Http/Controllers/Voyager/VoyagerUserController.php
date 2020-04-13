<?php

namespace App\Http\Controllers\Voyager;

//use TCG\Voyager\Http\Controllers\VoyagerUserController as BaseVoyagerUserController;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataDeleted;
use TCG\Voyager\Events\BreadDataRestored;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Events\BreadImagesDeleted;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;
use App\User;
use App\Reportuser;
use App\UserCoin;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;


class VoyagerUserController extends VoyagerBaseController

{
     use BreadRelationshipParser;       
        //***************************************
    //               ____
    //              |  _ \
    //              | |_) |
    //              |  _ <
    //              | |_) |
    //              |____/
    //
    //      Browse our Data Type (B)READ
    //
    //****************************************


    /**
     * Activate / Deactivate user.
     *
     * @param  Request  $request
     * @return view
     * @return success message
    */
    public function changeActivationStatus(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        $user->is_active = $request->status;
        $user->save();

        if($request->status == 0){
            return redirect()
                ->back()
                ->with([
                    'message'    => "User Deactivated Successfully",
                    'alert-type' => 'success',
                ]);
        }else{
            return redirect()
                ->back()
                ->with([
                    'message'    => "User Activated Successfully",
                    'alert-type' => 'success',
                ]);
        }
    }

    /**
     * Block / Unblock user.
     *
     * @param  Request  $request
     * @return view
     * @return success message
    */
    public function changeBlockStatus(Request $request)
    {

        $user = User::where('id', $request->user_id)->first();
        $user->is_block = $request->status;
        $user->save();

        if($request->status == 0){
            return redirect()
                ->back()
                ->with([
                    'message'    => "User Unblocked Successfully",
                    'alert-type' => 'success',
                ]);
        }else{
            return redirect()
                ->back()
                ->with([
                    'message'    => "User Blocked Successfully",
                    'alert-type' => 'success',
                ]);
        }
    }


     public function destroy(Request $request, $id)
    {
       
        $slug = $this->getSlug($request);

         $deletedRows = Reportuser::where('user_id',$id)->delete();
         $deleted_coinHistory = UserCoin::where('user_id',$id)->delete();


        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Init array of IDs
        $ids = [];
        if (empty($id)) {
            // Bulk delete, get IDs from POST
            $ids = explode(',', $request->ids);
        } else {
            // Single item delete, get ID from URL
            $ids[] = $id;
        }
        foreach ($ids as $id) {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);

            // Check permission
            $this->authorize('delete', $data);

            $model = app($dataType->model_name);
            if (!($model && in_array(SoftDeletes::class, class_uses_recursive($model)))) {
                $this->cleanup($dataType, $data);
            }
        }

        $displayName = count($ids) > 1 ? $dataType->getTranslatedAttribute('display_name_plural') : $dataType->getTranslatedAttribute('display_name_singular');

        $res = $data->destroy($ids);
        $data = $res
            ? [
                'message'    => __('voyager::generic.successfully_deleted')." {$displayName}",
                'alert-type' => 'success',
            ]
            : [
                'message'    => __('voyager::generic.error_deleting')." {$displayName}",
                'alert-type' => 'error',
            ];

        if ($res) {
            event(new BreadDataDeleted($dataType, $data));
        }

        return redirect()->route("voyager.{$dataType->slug}.index")->with($data);
    }

}
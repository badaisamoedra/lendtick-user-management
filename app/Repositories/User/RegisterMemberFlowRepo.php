<?php

namespace App\Repositories\User;

use App\Models\Master\RoleMaster as Role;
use App\Models\Master\RegisterMemberFlowMaster as MasterRegisterFlow;
use App\Models\User\RegisterMemberFlowManagement as RegisterFlow;
use Illuminate\Database\QueryException;
use DB;

class RegisterMemberFlowRepo{
	
	public static function all($columns = array('*')){
		try {
			if($columns == array('*')) return RegisterFlow::all();
			else return RegisterFlow::select($columns)->get();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function getByParam($column, $value){
		try {
			return RegisterFlow::where($column, $value)->get();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function create(array $data){
		try {
			return RegisterFlow::create($data);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function find($column, $value){
		try {
			return RegisterFlow::where($column, $value)->first();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function update($id, array $data){
		try { 
			return RegisterFlow::where('id_register_member_flow',$id)->update($data);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}	
	} 

	public static function delete($id){
		try { 
			return RegisterFlow::where('id_register_member_flow',$id)->delete();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
		
	}
	
	public static function deleteByParam($column, $value){
		try {
			return RegisterFlow::where($column, $value)->delete();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function last(){
		try{
			return RegisterFlow::orderBy('id_register_member_flow', 'desc')->first();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }
    
    public static function flow($id){
        try{
            // get count by role
            $role = Role::where('is_front_end',0)->where('status',1)->get();
            // get user with count approval in register member flow
            $user = [];
            if($role->count() > 0)
                foreach($role AS $row){
                    $tmp = DB::select(DB::raw("SELECT TOP 1 tables.id_user FROM (
                        SELECT a.id_user, COUNT(b.id_user) AS count_member FROM [user].[user] a
                        LEFT JOIN [user].[register_member_flow] b ON a.id_user=b.approve_by
                        WHERE a.id_role_master ='".$row->id_role_master."'
                        GROUP BY a.id_user, b.approve_by
                        ) tables ORDER BY tables.count_member ASC"));
                    if(isset($tmp[0]))
                        $user[$row->id_role_master] = $tmp[0]->id_user;
                }

            // insert into register member flow
            if(count($user) > 0)
                foreach($user AS $id_master_role => $approve_by){
                    // get master register member flow
                    $mstRegFlow = MasterRegisterFlow::where('id_role_master','=',$id_master_role)->get()->first();
                    // save data for approval
                    $regFlow = new RegisterFlow;
                    $regFlow->id_user = $id;
                    $regFlow->level = $mstRegFlow?$mstRegFlow->level:null;
                    $regFlow->id_master_register_member_flow = $mstRegFlow?$mstRegFlow->id_master_register_member_flow:null;
                    $regFlow->approve_by = $approve_by;
                    $regFlow->save();
                }
            return true;
        }catch(QueryException $e){
            throw new \Exception($e->getMessage(), 500);
        }
	}
	
	public static function approve_list($request, $d=null,$st=0,$l=10,$sr=null){
		if(is_numeric($st) && is_numeric($l)){

			$field = ["c.name", "c.phone_number", "c.npwp", "c.email", "c.loan_plafond", "c.microloan_plafond", "d.id_workflow_status"];
			// where inidication
			$where = [];

			$c_all = DB::select(DB::raw("
				SELECT COUNT(a.id_register_member_flow) AS cnt 
				FROM [user].[register_member_flow] a 
				JOIN [user].[master_register_member_flow] b ON a.id_master_register_member_flow=b.id_master_register_member_flow
				WHERE b.id_role_master='".$request->input('id_role_master')."' AND a.approve_at IS NULL "
			))[0]->cnt;

			if(!is_null($d) && !empty($d))
				foreach($field AS $i => $row){
					$where[] = $row." LIKE '%".$d."%'";
				}

			$c_fil = DB::select(DB::raw("
				SELECT COUNT(a.id_register_member_flow) AS cnt
				FROM [user].[register_member_flow] a 
				JOIN [user].[master_register_member_flow] b ON a.id_master_register_member_flow=b.id_master_register_member_flow
				JOIN [user].[user_profile] c ON a.id_user=c.id_user
				JOIN [user].[user_company] e ON c.id_user_profile=e.id_user_profile
				JOIN [user].[user] d ON a.id_user=d.id_user
				WHERE b.id_role_master='".$request->input('id_role_master')."' AND a.approve_at IS NULL ".(count($where)>0?"AND (".implode(' OR ', $where).")":"")
			))[0]->cnt;

			// order by
			$order = "";
			if(is_array($sr)){
				$order .= "ORDER BY ".$sr[0]." ".$sr[1];
			}

			// get length
			$length = [
				"OFFSET ".$st." ROWS",
				"FETCH NEXT ".$l." ROWS ONLY"
			];

			$data = DB::select(DB::raw("
				SELECT a.*, b.*, c.name, c.phone_number, c.npwp, c.email, c.loan_plafond, c.microloan_plafond, d.id_workflow_status, c.personal_photo, c.personal_identity_path, e.company_identity_path
				FROM [user].[register_member_flow] a 
				JOIN [user].[master_register_member_flow] b ON a.id_master_register_member_flow=b.id_master_register_member_flow
				JOIN [user].[user_profile] c ON a.id_user=c.id_user
				JOIN [user].[user_company] e ON c.id_user_profile=e.id_user_profile
				JOIN [user].[user] d ON a.id_user=d.id_user
				WHERE b.id_role_master='".$request->input('id_role_master')."' AND a.approve_at IS NULL ".(count($where)>0?"AND (".implode(' OR ', $where).")":"")." ".$order." ".$length[0]." ".$length[1]
			));

			return ['count_all'=>$c_all,'count_filter'=>$c_fil,'data'=>$data];
		}
		return [];
	} 
}
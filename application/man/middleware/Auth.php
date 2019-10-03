<?php
namespace app\man\middleware;

use app\man\model\Admin;
use think\Request;

class Auth
{
    public function handle(Request $request, \Closure $next)
    {

        $_auth = session('auth');
        if ($_auth) {
            $request->admin_id = $_auth['id'];
            $request->admin_account = $_auth['account'];

            if ($_auth['is_su'] !== 1) {
                $_defaultAccess = [
                    'index/index','index/pwd','index/logout'
                ];
                try {
                    $_admin = Admin::with('access')->find($_auth['id']);
                    $_access = array_column($_admin->access->toArray(),'action');
                    $_access = array_merge($_defaultAccess,$_access);
                    $_action = strtolower($request->controller() . '/' . $request->action());
                    if (!in_array($_action,$_access)) {
                        return redirect('Index/noneAuth');
                    }
                } catch (\Exception $e) {
                    return redirect('Index/noneAuth');
                }
            }

            return $next($request);
        }

        return redirect('Normal/login');
    }
}
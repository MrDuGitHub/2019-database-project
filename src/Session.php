<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/2/9
 * Time: 15:13
 */

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

class Session
{
    //此类的所有方法必须在无任何输出的时候执行才有效

    private function __construct()
    {
    }

    public static function start_readonly_session(): void
    {
        if (!isset($_SESSION)) {
            //为了防止被GC干掉，time还是会更新的。
            self::start_active_session();
            session_write_close();
        }
    }

    public static function start_active_session(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start(
                array(
                    'name' => COOKIE_PREFIX . 'PHPSESSID',
                    'cookie_httponly' => true,
                    'cookie_domain' => DOMAIN,
                    'gc_maxlifetime' => GC_MAXLIFETIME,
                    'cookie_lifetime' => COOKIE_LIFETIME
                )
            );

            //不加这个可能会被GC干掉
            $_SESSION['time'] = time();
        }
    }

    public static function is_logged_in(): bool
    {
        self::start_readonly_session();
        return isset($_SESSION['uid']);
    }

    public static function require_non_visitor(): void
    {
        self::require_logged_in();
        self::require_condition(!self::is_visitor());
    }

    public static function require_student_admin(): void
    {
        self::require_logged_in();
        self::require_condition(self::is_student_admin());
    }

    public static function require_teacher_admin(): void
    {
        self::require_logged_in();
        self::require_condition(self::is_teacher_admin());
    }

    public static function require_root_admin(): void
    {
        self::require_logged_in();
        self::require_condition(self::is_root_admin());
    }

    public static function is_student_admin(): bool
    {
        self::start_readonly_session();
        if (!self::is_logged_in()) return false;
        return ($_SESSION['is_student_admin']);
    }

    public static function is_root_admin(): bool
    {
        self::start_readonly_session();
        if (!self::is_logged_in()) return false;
        return ($_SESSION['is_root_admin']);
    }

    public static function is_teacher_admin(): bool
    {
        self::start_readonly_session();
        if (!self::is_logged_in()) return false;
        return ($_SESSION['is_teacher_admin']);
    }

    public static function is_student(): bool
    {
        self::start_readonly_session();
        if (!self::is_logged_in()) return false;
        return ($_SESSION['is_student']);
    }

    public static function is_visitor(): bool
    {
        self::start_readonly_session();
        if (!self::is_logged_in()) return false;
        return ($_SESSION['is_visitor']);
    }


    public static function is_teacher(): bool
    {
        return !self::is_student();
    }

    /**
     * 判断已登录用户的 UID 是否为 target_uid
     * @param int $target_uid
     * @return bool
     */
    public static function is_oneself(int $target_uid): bool
    {
        return Session::get_user_uid() === $target_uid;
    }

    /**
     * 已登录用户可以修改目标用户的资料，包括姓名、邮箱等。也可以查看目标用户的资料。
     * @param int $target_uid
     * @return bool
     * @throws \Exception
     */
    public static function is_able_to_manage_user_level_1(int $target_uid): bool
    {
        return Session::is_able_to_manage_user_level_2($target_uid) || Session::is_oneself($target_uid);
    }

    /**
     * 要求当前用户对目标用户有 level_1 的管理权限，否则返回403。
     * @param int $target_uid
     * @throws \Exception
     */
    public static function require_is_able_to_manage_user_level_1(int $target_uid): void
    {
        Session::require_logged_in();
        Session::require_condition(Session::is_able_to_manage_user_level_1($target_uid));
    }

    /**
     * 已登录用户可以管理目标用户，包括删除、重置密码等。
     * @param int $target_uid
     * @return bool
     * @throws \Exception
     */
    public static function is_able_to_manage_user_level_2(int $target_uid): bool
    {
        if (Session::is_root_admin()) {
            return true;
        } else {
            $target_user = new User($target_uid);
            if ($target_user->is_student()) {
                return (Session::is_student_admin());
            } else {
                return (Session::is_teacher_admin());
            }
        }
    }

    /**
     * 要求当前用户对目标用户有 level_2 的管理权限，否则返回403。
     * @param int $target_uid
     * @throws \Exception
     */
    public static function require_is_able_to_manage_user_level_2(int $target_uid): void
    {
        Session::require_logged_in();
        Session::require_condition(Session::is_able_to_manage_user_level_2($target_uid));
    }

    /**
     * @deprecated
     * @return bool
     */
    public static function is_user_prohibited_from_public_query(): bool
    {
        return self::is_teacher() || self::is_visitor();
    }

    /**
     * 检查用户是否登录，否则跳转到登录页面。
     */
    public static function require_logged_in(): void
    {
        if (!self::is_logged_in()) {
            header('Location:/login.php');
            exit();
        }
    }


    //不满足指定条件就返回403
    public static function require_condition(bool $val): void
    {
        if (!$val) {
            Template::die_403();
        }
    }

    /**
     * 判断客户端浏览器是否为 IE。
     * @return bool
     */
    public static function is_ie(): bool
    {
        return isset($_SERVER['HTTP_USER_AGENT']) &&
            (
                (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) ||
                (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0') !== false)
            );
    }

    /**
     * 如果客户端浏览器是 IE，302 跳转至警告页面。
     */
    public static function require_non_ie(): void
    {
        if (self::is_ie()) {
            Template::die_302('/compatibility/index.php');
        }
    }

    /**
     * 获取已登录用户的 UID。若未登录，返回 0。
     * @return int
     */
    public static function get_user_uid(): int
    {
        self::start_readonly_session();
        return intval($_SESSION['uid'] ?? 0);
    }


    public static function get_user_name(): string
    {
        self::start_readonly_session();
        assert(self::is_logged_in());
        return $_SESSION['name'];
    }


    /**
     * 清空整个 Session。相当于注销。
     */
    public static function destroy_session(): void
    {
        self::start_active_session();
        session_destroy();
    }


    /**
     * 在已经登录的状态下重载各项数据，写入 Session。
     * @param int $uid
     * @throws \Exception 当 SQL 语句执行失败时抛出异常。
     */
    public static function reload_session(int $uid = 0): void
    {
        if ($uid === 0) $uid = self::get_user_uid();
        self::load_session($uid);
    }

    /**
     * 加载该用户数据，写入 Session。<br/>
     * 此方法应在登录时，验证密码通过后调用。
     * @param int $uid
     * @throws \Exception 当 SQL 语句执行失败时抛出异常。
     */
    public static function load_session(int $uid): void
    {
        $user = new User($uid);
        //$email = $user->get_email();
        $name = $user->get_name();
        $is_student = $user->is_student();
        $is_visitor = $user->is_visitor();
        $is_student_admin = (new Privilege\IsStudentAdmin($uid))->get_value();
        $is_teacher_admin = (new Privilege\IsTeacherAdmin($uid))->get_value();
        $is_root_admin = (new Privilege\IsRootAdmin($uid))->get_value();

        //写入session
        self::start_active_session();
        $_SESSION['uid'] = $uid;
        $_SESSION['name'] = $name;
        //$_SESSION['email'] = $email;
        $_SESSION['is_student'] = $is_student;
        $_SESSION['is_visitor'] = $is_visitor;
        $_SESSION['is_student_admin'] = $is_student_admin;
        $_SESSION['is_teacher_admin'] = $is_teacher_admin;
        $_SESSION['is_root_admin'] = $is_root_admin;
        session_write_close();
    }

    /**
     * 返回客户端的 IPv4 或 IPv6 地址。
     * @return string
     */
    public static function get_client_address(): string
    {
        /*
         * 不考虑代理IP，否则客户端可以伪造IP或者进行XSS
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        } else if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (!empty($_SERVER["REMOTE_ADDR"])) {
            return $_SERVER["REMOTE_ADDR"];
        } else {
            return '';
        }
        */
        return $_SERVER["REMOTE_ADDR"];
    }

    /**
     * 返回当前登录的用户的User对象。
     * @return User
     */
    public static function get_current_user(): User
    {
        $uid = self::get_user_uid();
        $user = new User($uid);
        return $user;
    }
}
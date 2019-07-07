<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/2/23
 * Time: 22:53
 */

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

Session::require_non_ie();
Session::require_non_visitor();

//try {
//    $conn = MySQL::get_instance();
//
//    $result = $conn->prepare_no_bind_query(
//        'SELECT uid,email,name FROM user WHERE is_student = 1'
//    );
//
//} catch (\Exception $e) {
//    Template::panic($e);
//    die();
//}

?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php Template::write_standard_head(); ?>
    <title>题库</title>
    <script>
        $(document).ready(function () {
            $('#nav-item-exercise').addClass('active');
        });
    </script>
    <script src="/js/alasql/alasql.min.js"></script>
    <script src="/js/vue/vue.js"></script>
    <style>
        body .content {
            margin-top: 1.6em;
            margin-bottom: 1em;
            position: relative;
            padding-bottom: 1.5em;
            border-top: 5px solid #fff;
            border-radius: .15em .15em .15em .15em;
            background: #f6f5f1;
            box-shadow: 0 0 30px rgba(0, 0, 0, .1)
        }

        body .content .header {
            position: relative;
            height: 5.25em;
            line-height: 5.25em
        }

        body .content .header .site_logo {
            margin-right: 1em;
            margin-left: 1.5em
        }

        body .content .header .site_name {
            top: 0;
            position: relative;
            top: .125em;
            font: bold 1.625em Ruda, Segoe UI, Calibri, Trebuchet MS, Helvetica, Verdana, sans-serif;
            line-height: .875em
        }

        body .content .header a {
            text-decoration: none
        }

        body .content .header .site_name .highlight {
            color: #2074e7
        }

        .hide-max-md {
            display: none
        }

        body .content .header .menu_buttons {
            float: right;
            margin-right: .5em
        }

        body .content .header .menu_buttons .menu_button {
            padding: 1.5em .5em;
            text-align: center;
            font: 1.125em 'Ruda', Segoe UI, Calibri, Trebuchet MS, Helvetica, Verdana, sans-serif;
            transition: color 225ms;
            -ms-transition: color 225ms
        }

        body .content .header a, body .content .header div {
            display: inline-block;
            color: #4a535f;
            vertical-align: middle
        }

        body .content .menu_container {
            margin-top: -.5em;
            width: 17em;
            position: absolute;
            right: 0;
            z-index: 99999;
            display: none;
            padding: .75em 1em;
            border-radius: 0 0 .25em .25em;
            background: #f9f8f4;
            box-shadow: 0 0 30px rgba(0, 0, 0, .3)
        }

        body .content .menu_container .menu .title:first-of-type {
            padding-top: .25em
        }

        body .content .menu_container .menu .title {
            padding: .75em 0 .25em;
            color: #444;
            font: 1.25em 'Ruda', Segoe UI, Calibri, Trebuchet MS, Helvetica, Verdana, sans-serif
        }

        body .content .menu_container .menu a.active {
            font-weight: 700
        }

        body .content .menu_container .menu a {
            display: block;
            padding: .125em 0 .125em .5em;
            font-size: 1.125em;
            line-height: 1.1625em
        }

        a {
            color: #186bdd
        }

        body .content .lesson .title {
            padding: .5em 26px;
            color: #394148;
            font: bold 1.6em Ruda, Segoe UI, Calibri, Trebuchet MS, Helvetica, Verdana, sans-serif
        }

        body .content .lesson .body {
            padding: 0 26px;
            color: #000;
            font-size: 1.15em
        }

        body .content .lesson .body h1 {
            margin-top: 1.5em;
            padding: .25em 0 .25em .5em;
            border-bottom: .1875em solid #eae9de;
            border-left: .1875em solid #eae9de;
            font: 1.125em 'Ruda', Segoe UI, Calibri, Trebuchet MS, Helvetica, Verdana, sans-serif
        }

        .btn-success {
            transition: background-color 225ms;
            -ms-transition: background-color 225ms
        }

        body .content .disabled_exercise_overlay {
            display: none;
            margin: .5em 0 1em;
            padding: 1.75em 1em;
            background-color: #eae8de;
            color: #000;
            text-align: center;
            font-size: 1.4em
        }

        body .content .exercise {
            margin: .5em auto 0;
            padding: 19.5px 26px 26px;
            border-radius: .25em .25em .25em .25em;
            background: #f0ede5
        }

        body .content .exercise .body .datatable_title {
            overflow-x: hidden;
            padding-right: 0;
            padding-left: 0;
            text-transform: none;
            white-space: nowrap
        }

        body .content .datatable table tr:first-child {
            border-bottom: 1px solid #dad9ce;
            background: #dad9ce
        }

        body .content .exercise .body .table_and_input {
            height: 25.1875em
        }

        body .content .exercise .body {
            margin: 0;
            font-size: 1.125em
        }

        body .content .datatable_title {
            padding: .5em 0;
            color: #757575;
            text-transform: none;
            font-size: .9375em
        }

        body .content .exercise .body .table_and_input {
            padding: 0;
            border: 1px solid #e6e5dc;
            background: #ebeae2
        }

        body .content .exercise .body .datatable {
            overflow-y: scroll;
            padding: 0
        }

        body .content .datatable table {
            margin-bottom: 0;
            font-size: .875em
        }

        body .content .datatable table tr:first-child td {
            font-weight: 700;
            font-size: 1.0625em
        }

        body .content .datatable table .column_name {
            color: #444;
            text-transform: capitalize;
            font-size: .875em
        }

        body .content .exercise .body .table_and_input .datatable {
            height: 16em
        }

        body .content .exercise .body .table_and_input .message {
            /*position: absolute;*/
            bottom: 10.3em;
            display: none;
            box-sizing: border-box;
            margin: .5em;
            padding: .25em .6em;
            background: #186bdd;
            color: #fff;
            font-size: .875em
        }

        body .content .exercise .body .table_and_input .sqlinput_container {
            position: relative;
            padding: .5em 0;
            border: 0;
            border-top: 1px solid #e6e5dc;
            background: #fff
        }

        body .content .exercise .body .table_and_input .sqlinput_container .sqlinput {
            width: 100%;
            height: 8em;
            white-space: pre;
            resize: none
        }

        body .content .exercise .body .table_and_input .sqlinput_container .clear {
            position: absolute;
            right: 0;
            bottom: .35em;
            z-index: 999;
            padding: .5em .75em;
            color: #ccc;
            text-decoration: none;
            font-size: .875em
        }

        body .content .exercise .body .table_and_input .sqlinput_container .auto {
            position: absolute;
            right: 1em;
            bottom: 0;
            padding: .5em .75em
        }

        body .content .exercise .body .tasks_and_continue {
            height: 24.6875em;
            position: relative;
            padding: 0;
            border: 1px solid #e6e5dc;
            border-width: 2px 1px 1px 0;
            background: #e2e0d5
        }

        body .content .exercise .body .tasks_and_continue .solve_hint {
            position: absolute;
            bottom: 5em;
            padding: 0 2em 1em 1.5em;
            color: #867d7d;
            font-size: .825em
        }

        body .content .exercise .body .tasks_and_continue .solve_hint .solution_trigger, body .content .exercise .body .tasks_and_continue .solve_hint .solution_trigger:visited {
            color: #186bdd;
            text-decoration: none
        }

        body .content .footer {
            padding: 1em 26px;
            color: #747474
        }

        body .content .exercise .body .tasks_and_continue .tasks_list li .completed {
            opacity: .5
        }

        body .content .exercise .body .tasks_and_continue .tasks_list li .completed .check {
            padding: 0 .5em;
            color: #39b54a
        }

        body .content .exercise .body .tasks_and_continue .continue {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 3em;
            background: #39b54a;
            color: #fff;
            /*background: #6cf;*/
            /*color: #eee;*/
            text-align: center;
            font-size: 1.35em;
            line-height: 3em;
            transition: background-color 225ms;
            -ms-transition: background-color 225ms
        }
    </style>
</head>
<body>

<?php
Template::write_navbar_div();
?>

<div class="container" id="vue_app">
    <div class="row content">
        <div class="row col-xs-12">
            <div class="col-xs-12">
                <div class="lesson">
                    <div class="title">
                        {{ problem}}
                    </div>
                    <!--                        <div class="body">-->
                    <!--                            <h1>练习</h1>-->
                    <!--                            <p>练习使用Select语句</p>-->
                    <!--                        </div>-->
                </div>
            </div>
            <div class="col-xs-12">
                <div class="exercise" exerciseid="ex1" style="position: relative;">
                    <div class="body row">
                        <div class="datatable_title col-xs-12 col-sm-6 col-md-6" v-for="v in table" text-transform:null>
                            {{v}}
                        </div>
                        <div class="datatable_title col-xs-12 col-sm-6 col-md-6">
                            <el-switch
                                    v-model="order"
                                    active-text="是否排序">
                            </el-switch>
                        </div>
                        <div class="table_and_input col-xs-12 col-sm-12 col-md-12">
                            <div class="message error" style="display: block;" v-if="is_error">{{error}}</div>
                            <textarea style="display:none">
                                    {{ result }}
                            </textarea>
                            <div class="sqlinput_container">
                                <!--                                    <a href="javascript:;" onclick="editor.setValue({{sql_init}});" class="clear">RESET</a>-->
                                <!--                                    <a href="javascript:;" onclick="vue.Submit();" class="clear">提交</a>-->
                                <div class="sqlinput" id="input" style="font-size: 1em;">
                                </div>
                            </div>
                            <!--                                <textarea class="sqlinput" v-model="sql" rows="10" cols="100"></textarea>-->
                            <div class="datatable" datatableid="movies">
                                <table class="table table-striped table-condensed">
                                    <tr>
                                        <td class="cloumn_name" v-for="v in columnList">
                                            {{v}}
                                        </td>
                                    </tr>
                                    <tr v-for="v in inforList">
                                        <td v-for="vv in v">
                                            {{vv}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        答案指纹 : {{hash}}
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="tasks_and_continue col-xs-12 col-sm-12 col-md-12" style="height: 3em;">
                            <a href="javascript:;" onclick="vue.submit()" class="continue disabled" id="next_lesson">
                                提交
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="/js/Element/index.css">
<script src="/js/Element/index.js"></script>
<script type="text/javascript" src="/js/highlight/highlight.min.js"></script>
<script type="text/javascript" src="/js/modernizr/modernizr.min.js"></script>
<script type="text/javascript" src="/js/ace/ace.js"></script>
<script type="text/javascript" src="/js/ace/mode-sql.js"></script>
<script type="text/javascript" src="/js/jssha256/jssha256.js"></script>
<script>
    var vue = new Vue
    (
        {
            el: '#vue_app',
            data:
                {
                    sql: '',
                    sql_init: "-- 在此键入 SQL 语句\nSELECT \"Hello, world!\"",
                    table_init: ["CREATE TABLE cities (city string, population number)",
                        "INSERT INTO cities VALUES ('Rome',2863223), ('Paris',2249975), ('Berlin',3517424),  ('Madrid',3041579)"],
                    is_error: false,
                    order: false,
                    error: '',
                    hash: '',
                    problem: '课堂练习',
                    table: ['cities (city string, population number)'],
                    answer_hash: [1817565841, 644194877],
                    columnList: [],   //字段
                    inforList: [],
                    editor: null
                },
            computed: {
                result: function () {
                    if (this.auto)
                        return this.submit();
                }
            },
            methods:
                {
                    init_table() {
                        for (let i in alasql.tables) {
                            alasql("drop table " + i);
                        }
                        for (let i = 0; i < this.table_init.length; i++)
                            alasql(this.table_init[i]);
                    },
                    init_edit() {
                        this.editor = ace.edit("input");
                        this.editor.$blockScrolling = Infinity;
                        this.editor.setFontSize(16);
                        this.editor.session.setMode("ace/mode/sql");
                        this.editor.renderer.setShowGutter(!1);
                        this.editor.renderer.setPrintMarginColumn(!1);
                        this.editor.renderer.setShowPrintMargin(!1);
                        this.editor.renderer.setPadding(16);
                        this.editor.setHighlightActiveLine(!1);
                        this.editor.getSession().setMode("ace/mode/sql");
                        this.editor.getSession().setUseWrapMode(!0);
                        this.editor.setValue(this.sql);
                        this.editor.clearSelection();
                        this.editor.blur();
                        this.editor.commands.removeCommand("gotoline");
                        this.editor.commands.removeCommand("find");
                        this.editor.commands.removeCommand("findnext");
                        this.editor.on("change", function execute(data) {
                            vue.is_error = false;
                            vue.sql = vue.editor.getValue();
                        });
                    },
                    init: function () {
                        this.init_table();
                        this.init_edit();
                        this.reset();
                        //this.submit();
                    },
                    isObj: function (object) {
                        return object && typeof(object) == 'object' && Object.prototype.toString.call(object).toLowerCase() == "[object object]";
                    },
                    isArray: function (object) {
                        return object && typeof(object) == 'object' && object.constructor == Array;
                    },
                    getLength: function (object) {
                        var count = 0;
                        for (var i in object) count++;
                        return count;
                    },
                    Compare: function (objA, objB) {
                        if (!this.isObj(objA) || !this.isObj(objB)) return false; //判断类型是否正确
                        if (this.getLength(objA) != this.getLength(objB)) return false; //判断长度是否一致
                        return this.CompareObj(objA, objB, true); //默认为true
                    },
                    CompareObj: function (objA, objB, flag) {
                        for (var key in objA) {
                            if (!flag) //跳出整个循环
                                break;
                            if (!objB.hasOwnProperty(key)) {
                                flag = false;
                                break;
                            }
                            if (!this.isArray(objA[key])) { //子级不是数组时,比较属性值
                                if (objB[key] != objA[key]) {
                                    flag = false;
                                    break;
                                }
                            } else {
                                if (!this.isArray(objB[key])) {
                                    flag = false;
                                    break;
                                }
                                var oA = objA[key],
                                    oB = objB[key];
                                if (oA.length != oB.length) {
                                    flag = false;
                                    break;
                                }
                                for (var k in oA) {
                                    if (!flag) //这里跳出循环是为了不让递归继续
                                        break;
                                    flag = this.CompareObj(oA[k], oB[k], flag);
                                }
                            }
                        }
                        return flag;
                    },
                    submit: function () {
                        let res;
                        try {
                            res = alasql(this.sql);
                        } catch (err) {
                            this.is_error = true;
                            this.error = err.toString();
                            if (this.error.length > 50) {
                                this.error = this.error.substr(0, 50) + '...'
                            }
                            return err.toString();
                        }
                        this.is_error = false;
                        this.inforList = res;
                        this.columnList = [];
                        for (var key in this.inforList[0])
                            this.columnList.push(key);
                        this.hash = this.get_hash(res)
                    },
                    compare_object: function () {
                        return function (object1, object2) {
                            let value1 = '';
                            let value2 = '';
                            for (let i in object1) {
                                value1 = value1 + JSON.stringify(object1[i]);
                                value2 = value2 + JSON.stringify(object2[i]);
                            }
                            if (value1 < value2) {
                                return -1;
                            } else if (value1 > value2) {
                                return 1;
                            } else {
                                return 0;
                            }
                        };
                    },
                    // sort: function (object) {
                    //     return object.sort(this.compare_object())
                    // },
                    get_hash: function (object, sort) {
                        let s = [];
                        for (let i = 0; i < object.length; i++) {
                            s.push(this.get_string(object[i]));
                        }
                        if (!this.order) s.sort();
                        let ss = '';
                        for (let i = 0; i < s.length; i++)
                            ss = ss + s[i];
                        let hash = SHA256_hash(ss);
                        //console.log(hash)
                        //console.log(hash.substr(0,10))
                        return hash.substr(0, 10);
                        // var hash  =  1315423911,i,ch;
                        // for (i = ss.length - 1; i >= 0; i--) {
                        // 	ch = ss.charCodeAt(i);
                        // 	hash ^= ((hash << 5) + ch + (hash >> 2));
                        // }
                        // return  (hash & 0x7FFFFFFF);
                    },
                    get_string: function (obj) {
                        let s = '';
                        for (let key in obj) {
                            if (!this.isArray(obj[key])) { //子级不是数组时,计算属性值
                                if (!this.isObj(obj[key]))
                                    s = s + JSON.stringify(key) + ':' + JSON.stringify(obj[key]) + ' ';
                                else
                                    s = s + JSON.stringify(key) + ':{' + this.get_string(obj[key]) + '} ';
                            } else {
                                let oA = objA[key];
                                for (let k in oA) {
                                    s = s + JSON.stringify(key) + ':{' + this.get_string(oA[k]) + '} ';
                                }
                            }
                        }
                        return s;
                    },
                    reset: function () {
                        this.editor.setValue(this.sql_init);
                    }
                }
        }
    );

    vue.init();

</script>

<?php
Template::write_standard_footer();
?>
</body>
</html>

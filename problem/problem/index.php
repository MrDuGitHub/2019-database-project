<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/2/23
 * Time: 22:53
 */

namespace orgName\xxSystem;

use orgName\xxSystem\Database\Answer;use orgName\xxSystem\Database\Question;use orgName\xxSystem\Database\QuestionFit;
require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

Session::require_non_ie();
Session::require_non_visitor();

try {
    $question_id = intval($_GET['question'] ?? 0);
    $question = Question::load_from_id($question_id);

    $question_fit_array = array();
    $question_fit_list = QuestionFit::get_all();
    foreach ($question_fit_list as $question_fit) {
        array_push(
            $question_fit_array,
            array('content' => $question_fit->name,
                'url' => 'index.php?question=' . $question_fit->id)
        );
    }
    unset($question_fit_list);

    $answer_sql = '';
    try {
        $answer = Answer::load_answer(Session::get_user_uid(), $question_id);
        $answer_sql = $answer->answer_sql;
    } catch (\Exception $e) {
        //do nothing
    } finally {
        unset($answer);
    }

} catch (\Exception $e) {
    Template::panic($e);
    die();
}

?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php Template::write_standard_head(); ?>
    <title><?= $question->id ?> - <?= htmlentities($question->name) ?></title>
    <script>
        $(document).ready(function () {
            $('#nav-item-problem').addClass('active');
        });
    </script>
    <script src="/js/alasql/alasql.min.js"></script>
    <script src="/js/vue/vue.js"></script>
    <link type="text/css" href="/js/Element/index.css" rel="stylesheet">
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
            position: absolute;
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
        <div class="row col-xs-10">
            <div class="col-xs-12">
                <div class="lesson">
                    <div class="title">
                        {{ problem}}
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="exercise" style="position: relative;">
                    <div class="body row">
                        <div class="datatable_title col-xs-12 col-sm-8 col-md-8" v-for="v in table" text-transform:null>
                            {{v}}
                        </div>
                        <el-switch
                                v-model="auto"
                                active-text="自动提交">
                        </el-switch>
                        <div class="table_and_input col-xs-12 col-sm-8 col-md-8" >
                            <div class="message error" style="display: block;" v-if="is_error">{{error}}</div>
                            <div class="datatable">
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
                                </table>
                            </div>
                            <textarea style="display:none">
                                    {{ result }}
                                </textarea>
                            <div class="sqlinput_container">
                                <a href="javascript:;" onclick="vue.reset();" class="clear">RESET</a>
                                <div class="sqlinput" id="input" style="font-size: 1em;">
                                </div>
                            </div>

                        </div>
                        <div class="tasks_and_continue col-xs-12 col-sm-4 col-md-4">
                            <div id="task_description">
                                {{task}}
                            </div>
                            <div id="task_1">

                            </div>
                            <!--                            <ol class="tasks_list">-->
                            <!--                                <li v-for="(task,index) in tasks" :id="'task_'+index" v-html="task">-->
                            <!---->
                            <!--                                </li>-->
                            <!--                            </ol>-->

                            <a href="javascript:;"
                               onclick="if(!$('#submit-button').prop('disabled')){$('#submit-button').prop('disabled',true);vue.Submit()}"
                               class="continue" id="submit-button">
                                提交
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="lesson_menu menu col-xs-2">
            <div class="title">目录</div>
            <a v-for="value in contents" :href="value.url" :title="value.content">{{value.content}}<br></a>
        </div>
    </div>
</div>
<script src="/js/Element/index.js"></script>
<script src="/js/highlight/highlight.min.js"></script>
<script src="/js/modernizr/modernizr.min.js"></script>
<script src="/js/ace/ace.js"></script>
<script src="/js/ace/mode-sql.js"></script>
<script src="/js/jssha256/jssha256.js"></script>
<script>
    var vue = new Vue
    (
        {
            el: '#vue_app',
            data:
                {
                    sql: '',
                    sql_init: <?= json_encode(empty($answer_sql) ? "-- 在此键入 SQL 语句\nSELECT \"Hello, world!\"" : $answer_sql)?>,
                    table_init: <?= json_encode($question->preload_sql)?>,
                    is_error: false,
                    auto: false,
                    error: '',
                    contents: <?=json_encode($question_fit_array)?>,
                    problem: <?=json_encode($question->name)?>,
                    table: [<?=json_encode($question->table_digest)?>],
                    task: <?=json_encode($question->description)?>,
                    answer_hash:  <?=json_encode($question->answer_hash)?> ,
                    columnList: [],   //字段
                    inforList: [],
                    editor: null
                },
            computed: {
                result: function () {
                    if (this.auto) {
                        console.log("Executing SQL");
                        return this.Submit();
                    }
                }
            },
            methods:
                {
                    init_table() {
                        for (let i in alasql.tables) {
                            alasql("drop table " + i);
                        }
                        alasql(this.table_init);
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
                        //this.Submit()
                    },
                    isObj: function (object) {
                        return object && typeof (object) == 'object' && Object.prototype.toString.call(object).toLowerCase() == "[object object]";
                    },
                    isArray: function (object) {
                        return object && typeof (object) == 'object' && object.constructor == Array;
                    },
                    getLength: function (object) {
                        let count = 0;
                        for (let i in object) count++;
                        return count;
                    },
                    Compare: function (objA, objB) {
                        if (!this.isObj(objA) || !this.isObj(objB)) return false; //判断类型是否正确
                        if (this.getLength(objA) !== this.getLength(objB)) return false; //判断长度是否一致
                        return this.CompareObj(objA, objB, true); //默认为true
                    },
                    CompareObj: function (objA, objB, flag) {
                        for (let key in objA) {
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
                                if (oA.length !== oB.length) {
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
                    Submit: function () {
                        var res;
                        try {
                            res = alasql(this.sql);
                        } catch (err) {
                            this.is_error = true;
                            this.error = err.toString();
                            if (this.error.length > 50) {
                                this.error = this.error.substr(0, 50) + '...'
                            }

                            this.init_table();
                            $('#submit-button').prop('disabled', false);

                            return err.toString();
                        }
                        this.is_error = false;
                        this.inforList = res;
                        if (this.get_hash(res) === this.answer_hash) {
                            //document.getElementById('task_1').innerHTML = '<span class="completed">' + document.getElementById('task_1').innerHTML + '<span class="check">✓</span></span>';
                            document.getElementById('task_1').innerHTML = '<div class="alert alert-info">答案正确。正在提交，请稍候……</div>';
                            //提交成绩
                            $.post('/api/problem/submit-answer.php', JSON.stringify({
                                'question_id': <?=$question->id ?>,
                                'answer': this.sql,
                                'answer_is_correct': 1
                            }), function (result) {
                                let type = result['result'];
                                if (type === 'failure') type = 'danger';
                                document.getElementById('task_1').innerHTML = ('<div class="alert alert-' + type + '">' + _.escape(result['message']) + '</div>');
                                $('#submit-button').prop('disabled', false);
                            }, 'json');

                        } else {
                            //提交成绩？暂不
                            $('#submit-button').prop('disabled', false);

                        }
                        this.columnList = [];
                        for (let key in this.inforList[0])
                            this.columnList.push(key);
                        //console.log(alasql.tables)
                        this.init_table();
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
                    sort: function (object) {
                        //todo
                        return object.sort(this.compare_object())
                    },
                    get_hash: function (object, sort) {
                        let s = [];
                        for (let i = 0; i < object.length; i++) {
                            s.push(this.get_string(object[i]));
                        }
                        if (sort) {
                            s.sort();
                        }
                        let ss = '';
                        for (let i = 0; i < s.length; i++) {
                            ss = ss + s[i];
                        }
                        let hash = SHA256_hash(ss);
                        console.log(hash);
                        console.log(hash.substr(0, 10));
                        return hash.substr(0, 10);
                    },
                    get_string: function (obj) {
                        let s = '';
                        for (let key in obj) {
                            if (!this.isArray(obj[key])) { //子级不是数组时,计算属性值
                                s = s + JSON.stringify(key) + ':' + JSON.stringify(obj[key]) + ' ';
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
                        //document.getElementById('task_description').innerHTML = this.task
                    }
                }
        }
    );

    vue.init();

</script>
</body>
</html>

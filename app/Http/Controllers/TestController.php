<?php

namespace App\Http\Controllers;

use App\Test;
use App\Topic;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $content = collect($request->ans)->toJson();
        // $content = json_encode($request->ans, 256);

        //取得題數
        $show_num = collect($request->ans)->count();
        //答對題數
        $right_ans = 0;

        foreach ($request->ans as $topic_id => $ans) {
            $topic = Topic::find($topic_id);
            if ($topic->ans == $ans) {
                $right_ans++;
            }
        }
        //依據答對比例算分
        $score = round(100 * ($right_ans / $show_num), 0);

        $stu_info = session('pref/language');

        $test = Test::create([
            'content' => $content,
            'user_id' => $request->user_id,
            'exam_id' => $request->exam_id,
            'score'   => $score,
            'grade'   => substr($stu_info, 0, 2),
            'class'   => substr($stu_info, 2, 2),
            'num'     => substr($stu_info, 4, 2),
        ]);

        return redirect()->route('test.show', $test->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Test $test)
    {

        //從json解開成「$topics[題號]=使用者填答」的陣列
        $topics = json_decode($test->content, true);

        // dd($test->exam->topics->find(array_keys($topics)));
        //要送到考卷結果的所有內容
        $content = [];

        //題號
        $i = 1;
        //將$topics陣列一筆一筆抽出成：題號 $topic_id 及 使用者填答 $ans
        foreach ($topics as $topic_id => $ans) {
            //根據 $topic_id 抓取完整題目資訊
            // $content[$i]['topic'] = Topic::find($topic_id);
            // $content[$i]['ans']   = $ans;

            $content[$i] = [
                'topic' => Topic::find($topic_id),
                'ans'   => $ans,
            ];

            $i++;
        }

        return view('exam.test', compact('test', 'content'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

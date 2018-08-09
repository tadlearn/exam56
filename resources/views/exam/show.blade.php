@extends('layouts.app') 

@section('content')
    <h1>
        {{ $exam->title }}
        @can('建立測驗')
            <a href="#" class="btn btn-danger btn-del-exam" data-id="{{ $exam->id }}">刪除</a>
            <a href="{{ route('exam.edit', $exam->id) }}" class="btn btn-warning">編輯</a>
        @endcan
    </h1>

    {{-- 題目表單 --}}
    @can('建立測驗')
        @if(isset($topic))
            {{ bs()->openForm('patch', "/topic/{$topic->id}", ['model' => $topic]) }}
        @else
            {{ bs()->openForm('post', '/topic') }}
        @endif

            {{ bs()->formGroup()
                    ->label('題目內容', false, 'text-sm-right')
                    ->control(bs()->textarea('topic')->placeholder('請輸入題目內容'))
                    ->showAsRow() }}
            {{ bs()->formGroup()
                    ->label('選項1', false, 'text-sm-right')
                    ->control(bs()->text('opt1')->placeholder('輸入選項1'))
                    ->showAsRow() }}
            {{ bs()->formGroup()
                    ->label('選項2', false, 'text-sm-right')
                    ->control(bs()->text('opt2')->placeholder('輸入選項2'))
                    ->showAsRow() }}
            {{ bs()->formGroup()
                    ->label('選項3', false, 'text-sm-right')
                    ->control(bs()->text('opt3')->placeholder('輸入選項3'))
                    ->showAsRow() }}
            {{ bs()->formGroup()
                    ->label('選項4', false, 'text-sm-right')
                    ->control(bs()->text('opt4')->placeholder('輸入選項4'))
                    ->showAsRow() }}

            {{ bs()->formGroup()
                ->label('正確解答', false, 'text-sm-right')
                ->control(bs()->radioGroup('ans', [1=>'1', 2=>'2', 3=>'3', 4=>'4'])
                        ->inline()
                        ->addRadioClass(['my-1','mx-3']))
                ->showAsRow() }}

            {{-- {{ bs()->formGroup()
                    ->label('正確解答', false, 'text-sm-right')
                    ->control(bs()->select('ans',[1=>1, 2=>2, 3=>3, 4=>4])->placeholder('請設定正確解答'))
                    ->showAsRow() }} --}}

            {{ bs()->hidden('exam_id', $exam->id) }}
            {{ bs()->formGroup()
                    ->label('')
                    ->control(bs()->submit('儲存'))
                    ->showAsRow() }}
        {{ bs()->closeForm() }}
    @endcan


    {{-- 題目列表 --}}
    @forelse($exam->topics as $key => $topic)
        <dl>
            <dt class="h3">
                
                @can('建立測驗')
                    {{-- <button type="button" class="btn btn-danger btn-del-topic" data-id="{{ $topic->id }}">刪除</button> --}}
                    <a href="#" class="btn btn-danger btn-del-topic" data-id="{{ $topic->id }}">刪除</a>
                    <a href="{{ route('topic.edit', $topic->id) }}" class="btn btn-warning">編輯</a>
                    （{{ $topic->ans }}）
                @endcan
                <span class="badge badge-success">{{ $key+1 }}</span>                
                {{ $topic->topic }}
            </dt>
            <dd class="opt">
                {{ bs()->radioGroup("ans[$topic->id]", [
                    1 => "&#10102; $topic->opt1", 
                    2 => "&#10103; $topic->opt2",  
                    3 => "&#10104; $topic->opt3",  
                    4 => "&#10105; $topic->opt4", 
                    ])
                    ->selectedOption((Auth::user() and Auth::user()->can('建立測驗'))?$topic->ans:0)
                    ->addRadioClass(['my-1','mx-3'])}}
            </dd>
        </dl>
    @empty
        <div class="alert alert-danger">
            尚無題目
        </div>
    @endforelse

    
    <div class="text-center">
        發佈於 {{ $exam->created_at->format("Y年m月d日 H:i:s") }} / 最後更新： {{ $exam->updated_at->format("Y年m月d日 H:i:s") }}
    </div>
@endsection


@section('js')
    <script>
        $(document).ready(function(){
            $('.btn-del-topic').click(function(){
                var topic_id=$(this).data('id');                
                swal({
                    title: "確定要刪除題目嗎？",
                    text: "刪除後該題目就消失救不回來囉！",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "是！含淚刪除！",
                    cancelButtonText: "不...別刪",
                }).then((result) => {
                    if (result.value) {                        
                        axios.delete('/topic/' + topic_id)
                        .then(function(){
                            return swal("OK！刪掉題目惹！", "該題目已經隨風而逝了...", "success");
                        }).then(function () {
                            location.reload();
                        });
                    }
                })
            });

            $('.btn-del-exam').click(function(){
                var exam_id=$(this).data('id');                
                swal({
                    title: "確定要刪除測驗嗎？",
                    text: "測驗刪除後該測驗及所有題目就消失救不回來囉！",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "是！含淚刪除！",
                    cancelButtonText: "不...別刪",
                }).then((result) => {
                    if (result.value) {                        
                        axios.delete('/exam/' + exam_id)
                        .then(function(){
                            return swal("OK！刪掉題目惹！", "該題目已經隨風而逝了...", "success");
                        }).then(function () {
                            location.href='/';
                        });
                    }
                })
            });
        });
    </script>
@endsection


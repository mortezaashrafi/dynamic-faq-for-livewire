<img src="https://mortezaashrafi.ir/dynamic-faq-for-livewire.jpg">

**Step 1**
install laravel
`composer create-project --prefer-dist laravel/laravel faq`

**Step 2**
create migration
`php artisan make:migration create_faq_table`

**Step 3**
edit migration (go to database)
`migrations  xxx_xx_xx_xxxxxx_create_faq_table.php`

Sample code
```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaqTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faq', function (Blueprint $table) {
            $table->id();
            //Question
            $table->string('question');
            //Answer
            $table->string('answer');
            //Type [post, page, product, xxx id]
            $table->string('type');
            //Type [post id, page id, product id, xxx id]
            $table->string('type_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faq');
    }
}
```
**Step 4**
env config database connect

**Step 5**
php artisan migrate

**Step 6**
Make laravel model
php artisan make:model Faq

**Step 7**
go to app Models Faq.php & edit file
Sample code
```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'type',
        'type_id',
    ];
}
```
**Step 8**
install livewire 
`composer require livewire/livewire`

**Step 9**
make livewire 
`php artisan make:livewire faq`

**Step 10**
go to app Http Livewire Faq.php

```
<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Livewire\Component;
use Illuminate\Http\Request;

class Faq extends Component
{
    public $contacts;

    public $question;
    public $answer;
    public $type;
    public $type_id;

    public $update_question;
    public $update_answer;
    public $update_type;
    public $update_type_id;

    public $faq_edit_id;

    public $inputs = [];
    public $types = ['post', 'page', 'product'];
    public $i = 1;

    /**
     * Write code on Method
     *
     * @param $i
     * @return void ()
     */
    public function add($i)
    {
        if ($i < 64)
        {
            $i = $i + 9;

        }else{
            $i = $i + 8;
        }
        $this->i = $i;
        array_push($this->inputs, $i);
    }

    /**
     * Write code on Method
     *
     * @param $i
     * @return void ()
     */
    public function remove($i)
    {
        unset($this->inputs[$i]);
    }

    /**
     * Write code on Method
     *
     * @param $faq_id
     * @return void ()
     */
    public function removeFaq($faq_id)
    {
        DB::table('faq')->where('id', $faq_id)->delete();
    }

    /**
     * Write code on Method
     *
     * @param $faq_id
     * @return void ()
     */
    public function editFaq($faq_id)
    {
        $this->faq_edit_id = $faq_id;
    }


    /**
     * Write code on Method
     *
     * @param $faq_id
     * @return void ()
     */
    public function updateFaq($faq_id)
    {
        $faqContent = DB::table('faq')->where('id', $faq_id)->get();
        $faqContentFirst = $faqContent->first();

        if ($this->update_question == null)
        {
            $this->update_question = $faqContentFirst->question;
        }
        if ($this->update_answer == null)
        {
            $this->update_answer = $faqContentFirst->answer;
        }
        if ($this->update_type == null)
        {
            $this->update_type = $faqContentFirst->type;
        }
        if ($this->update_type_id == null)
        {
            $this->update_type_id = $faqContentFirst->type_id;
        }
        DB::table('faq')
            ->where('id', $faq_id)
            ->update(
                [
                    'question' => $this->update_question,
                    'answer' => $this->update_answer,
                    'type' => $this->update_type,
                    'type_id' => $this->update_type_id,
                    'updated_at' => date('Y-m-d H:m:s'),
                ]
            );
        $this->faq_edit_id = 00;
    }

    /**
     * Write code on Method
     *
     * @return void ()
     */
    public function cancelFaq()
    {
        $this->faq_edit_id = 00;
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function render()
    {

        $this->contacts = DB::table('faq')->orderBy('id','DESC')->paginate(10)->items();
        $paginationContacts = DB::table('faq')->paginate(10);
        return view('livewire.faq',compact('paginationContacts'));
    }

    /**
     * Write code on Method
     *
     * @return void ()
     */
    private function resetInputFields()
    {
        $this->question = '';
        $this->answer = '';
        $this->type = '';
        $this->type_id = '';
    }

    /**
     * Write code on Method
     *
     * @return void ()
     */
    public function store()
    {
        $validatedDate = $this->validate([
            'question.0' => 'required',
            'answer.0' => 'required',
            'type.0' => 'required',
            'type_id.0' => 'required',

            'question.*' => 'required',
            'answer.*' => 'required',
            'type.*' => 'required',
            'type_id.*' => 'required',
        ],
            [
                'question.0.required' => 'question field is required',
                'answer.0.required' => 'answer field is required',
                'type.0.required' => 'type field is required',
                'type_id.0.required' => 'type id field is required',

                'question.*.required' => 'question field is required',
                'answer.*.required' => 'answer field is required',
                'type.*.required' => 'type field is required',
                'type_id.*.required' => 'type id field is required',
            ]
        );

        foreach ($this->question as $key => $value) {
            DB::table('faq')->insert(
                [
                    'question' => $this->question[$key],
                    'answer' => $this->answer[$key],
                    'type' => $this->type[$key],
                    'type_id' => $this->type_id[$key],
                    'created_at' => date('Y-m-d H:m:s'),
                    'updated_at' => date('Y-m-d H:m:s'),
                ]
            );
        }

        $this->inputs = [];

        $this->resetInputFields();

        session()->flash('message', 'Created Successfully.');
    }
}

```

**Step 11**
go to resources views livewire faq.blade.php

```
<div class="mt-5">
    <h2 class="mb-5">Dynamic faq for livewire</h2>
    @if(!empty($contacts))
        <table class="table table-bordered">
            <tr class="bg-light">
                <th>ID</th>
                <th>Question</th>
                <th>Answer</th>
                <th>Type</th>
                <th>Type ID</th>
                <th>Actions</th>
            </tr>
            @foreach($contacts as $key => $value)

                <tr>
                    <td>{{ $value->id }}</td>
                    <td>{{ $value->question }}</td>
                    <td>{{ $value->answer }}</td>
                    <td>{{ $value->type }}</td>
                    <td>{{ $value->type_id }}</td>
                    <td>
                        <button type="button" wire:click.prevent="removeFaq('{{ $value->id }}')"
                                class="btn btn-danger btn-sm">Remove
                        </button>
                        <button type="button" wire:click.prevent="editFaq('{{ $value->id }}')"
                                class="btn btn-secondary btn-sm">Edit
                        </button>
                    </td>
                </tr>

                @if($faq_edit_id == $value->id)
                    <tr class="bg-light">
                        <td>{{ $value->id }}</td>
                        <td>
                            <input type="text" class="form-control" placeholder="Enter Question"
                                   value="{{ $value->question }}"
                                   wire:change="$set('update_question',$event.target.value)">
                        </td>
                        <td>
                            <input type="text" class="form-control" placeholder="Enter Answer"
                                   value="{{ $value->answer }}"
                                   wire:change="$set('update_answer',$event.target.value)">
                        </td>
                        <td>
                            <div class="form-group">
                                <select class="form-select form-select" aria-label=".form-select-sm example"
                                        wire:change="$set('update_type',$event.target.value)">
                                    @foreach($types as $updateValue)
                                        <option value="{{$updateValue}}"
                                                @if($updateValue == $value->type) selected @endif>{{$updateValue}}</option>
                                    @endforeach
                                </select>
                                @error('type_update') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </td>

                        <td>
                            <input type="text" class="form-control"
                                   placeholder="Enter Type ID [post id, page id, product id, xxx id]"
                                   value="{{$value->type_id}}" wire:change="$set('update_type_id',$event.target.value)">
                        </td>
                        <td>
                            <button type="button" wire:click.prevent="updateFaq('{{ $value->id  }}')"
                                    class="btn btn-success btn-sm">Update
                            </button>
                            <button type="button" wire:click.prevent="cancelFaq()"
                                    class="btn btn-warning btn-sm">Cancel
                            </button>
                        </td>
                    </tr>
                @endif

            @endforeach
        </table>
        {{ $paginationContacts->links('vendor.pagination.bootstrap-4') }}
    @endif
    <form>
        <div class="add-input">
            <div class="col-12 d-flex">
                <button class="btn text-white btn-primary btn-sm my-3 " wire:click.prevent="add({{$i}})">Add new
                    faq
                </button>

            </div>
            <div>
                @if (session()->has('limit'))
                    <span class="text-danger">
                        {{ session('limit') }}
                    </span>
                @endif
            </div>
            <div class="row ">
                <!-- Question -->
                <div class="col-md-6 my-3">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Enter Question" wire:model="question.0">
                        @error('question.0') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <!-- Answer -->
                <div class="col-md-6 my-3">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Enter Answer" wire:model="answer.0">
                        @error('answer.0') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <!-- Type -->
                <div class="col-md-6 my-3">
                    <div class="form-group">
                        <select class="form-select form-select" aria-label=".form-select-sm example"
                                wire:model="type.0">
                            <option selected>Select Type</option>
                            @foreach($types as $value)
                                <option value="{{$value}}">{{$value}}</option>
                            @endforeach
                        </select>
                        @error('type.0') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <!-- Type ID -->
                <div class="col-md-6 my-3">
                    <div class="form-group">
                        <input type="text" class="form-control"
                               placeholder="Enter Type ID [post id, page id, product id, xxx id]"
                               wire:model="type_id.0">
                        @error('type_id.0') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        @foreach($inputs as $key => $value)
            <div class="add-input">
                <hr>
                <div class="row">
                    <!-- Question -->
                    <div class="col-md-6 my-3">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Enter Question"
                                   wire:model="question.{{ $value }}">
                            @error('question.'.$value) <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <!-- Answer -->
                    <div class="col-md-6 my-3">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Enter Answer"
                                   wire:model="answer.{{ $value }}">
                            @error('answer.'.$value) <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <!-- Type -->
                    <div class="col-md-6 my-3">
                        <div class="form-group">
                            <select class="form-select form-select" aria-label=".form-select-sm example"
                                    wire:model="type.{{ $value }}">
                                <option selected>Select Type</option>
                                @foreach($types as $val)
                                    <option value="{{$val}}">{{$val}}</option>
                                @endforeach
                            </select>
                            @error('type.'.$value) <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <!-- Type ID -->
                    <div class="col-md-6 my-3">
                        <div class="form-group">
                            <input type="text" class="form-control"
                                   placeholder="Enter Type ID [post id, page id, product id, xxx id]"
                                   wire:model="type_id.{{ $value }}">
                            @error('type_id.'.$value) <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-2 my-3">
                    <button class="btn btn-danger btn-sm" wire:click.prevent="remove({{$key}})">
                        Remove
                    </button>
                </div>
            </div>
        @endforeach

        <div class="row">
            <hr>
            <div class="col-md-12 my-3">
                <button type="button" wire:click.prevent="store()" class="btn btn-success btn-sm">Submit</button>
            </div>
        </div>

    </form>
    @if (session()->has('message'))
        <div class="alert alert-success m-5">
            {{ session('message') }}
        </div>
    @endif
</div>
```
**Step 12**
show views 

```
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    @livewireStyles
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Faq</title>
</head>
<body>

<div class="container">
    <livewire:faq/>
</div>

@livewireScripts
<!-- Optional JavaScript; choose one of the two! -->

<!-- Option 1: Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<!-- Option 2: Separate Popper and Bootstrap JS -->
<!--
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
-->
</body>
</html>
```

**Step 13**

`php artisan vendor:publish`
select Laravel-pagination and enter

**Step 14**
run serve
`php artisan serve`

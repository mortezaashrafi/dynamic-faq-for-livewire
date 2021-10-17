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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormRequest;
use App\Http\Resources\FormResource;
use App\Models\System\Form\Form;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function index(Request $request)
    {
        $forms = Form::withCount(['fields', 'responses'])
            ->with('creator:id,name')
            ->latest()
            ->paginate(10);

        return FormResource::collection($forms);
    }

    public function store(StoreFormRequest $request)
    {
        $form = Form::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return new FormResource($form->load('creator'));
    }

    public function show(Form $form)
    {
        $form->load(['fields.options', 'creator:id,name']);

        return new FormResource($form);
    }

    public function update(StoreFormRequest $request, Form $form)
    {
        $form->update($request->validated());

        return new FormResource($form->fresh()->load(['fields.options', 'creator:id,name']));
    }

    public function destroy(Form $form)
    {
        $form->delete();

        return response()->json([
            'message' => 'Form deleted successfully.',
        ]);
    }
}

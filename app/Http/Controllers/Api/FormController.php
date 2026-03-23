<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Form\StoreFormRequest;
use App\Http\Requests\Form\UpdateFormRequest;
use App\Http\Resources\FormResource;
use App\Models\System\Form\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormController extends Controller
{
    public function index(Request $request)
    {
        $forms = Form::query()
            ->with(['creator:id', 'creator.profile'])
            ->withCount(['fields', 'responses'])
            ->latest()
            ->paginate(10);

        return FormResource::collection($forms);
    }

    public function show(Form $form)
    {
        $form->load([
            'creator:id',
            'creator.profile',
            'fields.options',
        ]);

        return new FormResource($form);
    }

    public function store(StoreFormRequest $request)
    {
        $form = DB::transaction(function () use ($request) {
            $form = Form::create([
                'title' => $request->title,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active'),
                'is_public' => $request->boolean('is_public'),
                'created_by' => $request->user()->id,
            ]);

            foreach ($request->input('fields', []) as $fieldData) {
                $field = $form->fields()->create([
                    'type' => $fieldData['type'],
                    'label' => $fieldData['label'],
                    'description' => $fieldData['description'] ?? null,
                    'is_required' => (bool) $fieldData['is_required'],
                    'placeholder' => $fieldData['placeholder'] ?? null,
                    'sort_order' => $fieldData['sort_order'],
                    'validation_rules' => $fieldData['validation_rules'] ?? null,
                    'is_active' => (bool) $fieldData['is_active'],
                ]);

                foreach ($fieldData['options'] ?? [] as $optionData) {
                    $field->options()->create([
                        'label' => $optionData['label'],
                        'value' => $optionData['value'],
                        'sort_order' => $optionData['sort_order'],
                    ]);
                }
            }

            return $form;
        });

        $form->load([
            'creator:id',
            'creator.profile',
            'fields.options',
        ]);

        return (new FormResource($form))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateFormRequest $request, Form $form)
    {
        DB::transaction(function () use ($request, $form) {
            $form->update([
                'title' => $request->title,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active'),
                'is_public' => $request->boolean('is_public'),
            ]);

            // Simple and reliable for builder-based editing
            $form->fields()->delete();

            foreach ($request->input('fields', []) as $fieldData) {
                $field = $form->fields()->create([
                    'type' => $fieldData['type'],
                    'label' => $fieldData['label'],
                    'description' => $fieldData['description'] ?? null,
                    'is_required' => (bool) $fieldData['is_required'],
                    'placeholder' => $fieldData['placeholder'] ?? null,
                    'sort_order' => $fieldData['sort_order'],
                    'validation_rules' => $fieldData['validation_rules'] ?? null,
                    'is_active' => (bool) $fieldData['is_active'],
                ]);

                foreach ($fieldData['options'] ?? [] as $optionData) {
                    $field->options()->create([
                        'label' => $optionData['label'],
                        'value' => $optionData['value'],
                        'sort_order' => $optionData['sort_order'],
                    ]);
                }
            }
        });

        $form->load([
            'creator:id',
            'creator.profile',
            'fields.options',
        ]);

        return new FormResource($form);
    }

    public function destroy(Form $form)
    {
        $form->delete();

        return response()->json([
            'message' => 'Form deleted successfully.',
        ]);
    }
}

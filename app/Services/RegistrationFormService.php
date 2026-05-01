<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegistrationFormService
{
    public const FIELD_TYPES = [
        'text',
        'email',
        'tel',
        'textarea',
        'select',
        'radio',
        'checkbox',
        'number',
        'date',
    ];

    public const FIELD_WIDTHS = [
        'full',
        'half',
        'third',
    ];

    public function normalizeFields(?array $fields): array
    {
        return collect($fields ?? [])
            ->map(function ($field, int $index) {
                $label = trim((string) Arr::get($field, 'label', ''));

                // Prefer an existing ASCII key from the stored field data;
                // fall back to generating one from the label via slug.
                $storedKey = trim((string) Arr::get($field, 'key', ''));
                if ($storedKey !== '' && preg_match('/^[a-z0-9_]+$/', $storedKey)) {
                    // Already a clean snake_case ASCII key — use it directly.
                    $key = $storedKey;
                } else {
                    // Derive key from label (works for English labels only).
                    $derivedKey = $storedKey ?: $label ?: 'field_' . ($index + 1);
                    $key = Str::snake(Str::slug($derivedKey, '_'));
                }

                // Skip fields that have no label or produce an empty/invalid key.
                if ($label === '' || $key === '') {
                    return null;
                }

                // Skip built-in fields that are always rendered hardcoded in the view.
                $builtinKeys = ['guest_name', 'guest_email', 'full_name', 'email', 'name'];
                if (in_array($key, $builtinKeys, true)) {
                    return null;
                }

                $type = Arr::get($field, 'type', 'text');
                if (!in_array($type, self::FIELD_TYPES, true)) {
                    $type = 'text';
                }

                $width = Arr::get($field, 'width', 'full');
                if (!in_array($width, self::FIELD_WIDTHS, true)) {
                    $width = 'full';
                }

                $options = collect(Arr::get($field, 'options', []))
                    ->map(fn ($option) => trim((string) $option))
                    ->filter()
                    ->values()
                    ->all();

                return [
                    'key' => $key,
                    'label' => $label,
                    'type' => $type,
                    'required' => (bool) Arr::get($field, 'required', false),
                    'placeholder' => trim((string) Arr::get($field, 'placeholder', '')),
                    'help_text' => trim((string) Arr::get($field, 'help_text', '')),
                    'width' => $width,
                    'options' => in_array($type, ['select', 'radio'], true) ? $options : [],
                ];
            })
            ->filter()
            ->unique('key')
            ->values()
            ->all();
    }

    public function buildValidationRules(?array $fields): array
    {
        $rules = [];

        foreach ($this->normalizeFields($fields) as $field) {
            $key = 'form_payload.' . $field['key'];
            $fieldRules = $field['required'] ? ['required'] : ['nullable'];

            switch ($field['type']) {
                case 'email':
                    $fieldRules[] = 'email';
                    $fieldRules[] = 'max:255';
                    break;

                case 'tel':
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:40';
                    break;

                case 'textarea':
                case 'text':
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:5000';
                    break;

                case 'select':
                case 'radio':
                    $fieldRules[] = 'string';
                    $fieldRules[] = Rule::in($field['options']);
                    break;

                case 'checkbox':
                    $fieldRules[] = $field['required'] ? 'accepted' : 'boolean';
                    break;

                case 'number':
                    $fieldRules[] = 'numeric';
                    break;

                case 'date':
                    $fieldRules[] = 'date';
                    break;
            }

            $rules[$key] = $fieldRules;
        }

        return $rules;
    }

    public function normalizePayload(array $payload, ?array $fields): array
    {
        $normalized = [];

        foreach ($this->normalizeFields($fields) as $field) {
            $value = Arr::get($payload, $field['key']);

            if ($field['type'] === 'checkbox') {
                $normalized[$field['key']] = (bool) $value;
                continue;
            }

            if (is_string($value)) {
                $value = trim($value);
            }

            $normalized[$field['key']] = $value === '' ? null : $value;
        }

        return $normalized;
    }

    public function normalizeScheduleItems(?array $items): array
    {
        return collect($items ?? [])
            ->map(function ($item) {
                $title = trim((string) Arr::get($item, 'title', ''));

                if ($title === '') {
                    return null;
                }

                return [
                    'title' => $title,
                    'stage' => trim((string) Arr::get($item, 'stage', '')),
                    'start_time' => trim((string) Arr::get($item, 'start_time', '')),
                    'end_time' => trim((string) Arr::get($item, 'end_time', '')),
                    'description' => trim((string) Arr::get($item, 'description', '')),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
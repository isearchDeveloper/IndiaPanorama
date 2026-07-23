<?php

namespace App\Http\Requests;

use App\Models\MenuItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMenuItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'     => ['required', 'string', 'min:1', 'max:200'],
            'type'      => ['required', Rule::in(array_keys(MenuItem::TYPES))],
            'linked_id' => ['nullable', 'integer', 'min:1'],
            'url'       => ['nullable', 'string', 'max:500'],
            'target'    => ['nullable', Rule::in(['_self', '_blank'])],
            'status'    => ['nullable', 'integer', Rule::in([0, 1])],

            // ── Mega menu settings ──────────────────────────────
            'mega_settings'                  => ['nullable', 'array'],
            'mega_settings.content_type'     => ['nullable', 'string', Rule::in([MenuItem::CONTENT_NORMAL, MenuItem::CONTENT_MEGA])],
            'mega_settings.display_source'   => ['nullable', 'string', Rule::in(array_keys(MenuItem::MEGA_SOURCES))],
            'mega_settings.display_mode'     => ['nullable', 'string', Rule::in(array_keys(MenuItem::MEGA_DISPLAY_MODES))],
            'mega_settings.linked_menu_id'   => ['nullable', 'integer', 'exists:menus,id'],
            'mega_settings.region_ids'       => ['nullable', 'array'],
            'mega_settings.region_ids.*'     => ['integer'],
            'mega_settings.state_ids'        => ['nullable', 'array'],
            'mega_settings.state_ids.*'      => ['integer'],
            'mega_settings.active_only'      => ['nullable', 'boolean'],
            'mega_settings.package_only'     => ['nullable', 'boolean'],
            'mega_settings.manage_city_only' => ['nullable', 'boolean'],
            'mega_settings.banner'           => ['nullable', 'array'],
            'mega_settings.banner.image'     => ['nullable', 'string', 'max:500'],
            'mega_settings.banner.alt'       => ['nullable', 'string', 'max:200'],
            'mega_settings.banner.title'     => ['nullable', 'string', 'max:200'],
            'mega_settings.banner.description' => ['nullable', 'string', 'max:1000'],
            'mega_settings.banner.cta_text'  => ['nullable', 'string', 'max:100'],
            'mega_settings.banner.cta_url'   => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please enter a display title for this item.',
            'title.max'      => 'Title must not exceed 200 characters.',
            'type.required'  => 'Please select a link type.',
            'type.in'        => 'Invalid link type selected.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'linked_id' => $this->input('linked_id') ?: null,
            'url'       => $this->input('url')        ?: null,
            'target'    => $this->input('target', '_self'),
            'status'    => (int) ($this->input('status', 1)),
        ]);
    }

    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $v) {
            $type     = $this->input('type');
            $linkedId = $this->input('linked_id');
            $url      = $this->input('url');

            if ($type === 'custom' && empty($url)) {
                $v->errors()->add('url', 'A URL is required when type is "Custom URL".');
            }

            if (in_array($type, MenuItem::LINKED_TYPES, true) && empty($linkedId)) {
                $v->errors()->add('linked_id', 'Please select a ' . $type . ' to link this item to.');
            }

            if ($type === 'menu_reference' && empty($linkedId)) {
                $v->errors()->add('linked_id', 'Please select a menu to reference.');
            }

            // Mega: custom_menu source requires linked_menu_id
            $mega = $this->input('mega_settings', []);
            if (
                ($mega['content_type'] ?? '') === MenuItem::CONTENT_MEGA &&
                ($mega['display_source'] ?? '') === MenuItem::MEGA_SOURCE_CUSTOM &&
                empty($mega['linked_menu_id'])
            ) {
                $v->errors()->add('mega_settings.linked_menu_id', 'Please select a menu for the mega menu content.');
            }
        });
    }
}

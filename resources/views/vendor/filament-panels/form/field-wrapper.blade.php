@props([
    'field' => null,
    'hasInlineLabel' => false,
    'isConcealed' => false,
    'isDisabled' => false,
    'isMarkedAsRequired' => false,
    'label' => null,
    'labelPrefix' => null,
    'labelSuffix' => null,
    'shouldAutosize' => false,
    'statePath' => null,
])

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :has-inline-label="$hasInlineLabel"
    :is-concealed="$isConcealed"
    :is-disabled="$isDisabled"
    :is-marked-as-required="$isMarkedAsRequired"
    :label="$label"
    :label-prefix="$labelPrefix"
    :label-suffix="$labelSuffix"
    :should-autosize="$shouldAutosize"
    :state-path="$statePath"
>
    {{ $slot }}
</x-dynamic-component> 
1. Create `resources/js/components/SearchableSelect.vue`.
   - Props: options, modelValue, label, placeholder, disabled.
   - Features: Filterable list, keyboard navigation (optional but good), clearable.
2. Update `EditScheduleModal.vue`.
   - Import `SearchableSelect`.
   - Replace `<select>` for Lecturer with `SearchableSelect`.
   - Replace `<select>` for Room with `SearchableSelect`.
   - Ensure v-model binding works correctly.
3. Verify.

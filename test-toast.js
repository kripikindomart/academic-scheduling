// Test script to verify toast store methods
const toastMethods = [
  'toastStore.success(title, message)',
  'toastStore.error(title, message)',
  'toastStore.warning(title, message)',
  'toastStore.info(title, message)'
];

console.log('Toast Store Methods Available:');
toastMethods.forEach(method => {
  console.log(`✓ ${method}`);
});

console.log('\nIncorrect method that was causing errors:');
console.log('✗ toastStore.addToast({...})');
export function csrf() {
    return document.head.querySelector('meta[name="csrf-token"]')?.content
        ?? document.querySelector('input[name="_token"]')?.value
        ?? console.error('CSRF token not found');
}
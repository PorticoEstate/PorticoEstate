export function GenerateRandomHash(length = 8) {
    return Array.from({length}, () => Math.floor(Math.random() * 36).toString(36)).join('');
}
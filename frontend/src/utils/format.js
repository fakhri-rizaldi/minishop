/**
 * Format integer rupiah to IDR currency string (e.g. Rp 185.000)
 */
export function formatRupiah(amount) {
  const num = Number(amount) || 0
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(num)
}

/**
 * Format ISO datetime string to Indonesian readable date (e.g. 30 Sep 2026, 12:45)
 */
export function formatDate(dateString) {
  if (!dateString) return '-'
  try {
    const date = new Date(dateString)
    return new Intl.DateTimeFormat('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    }).format(date)
  } catch {
    return dateString
  }
}

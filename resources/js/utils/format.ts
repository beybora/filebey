export function formatDate(date: string | null | undefined): string {
    if (!date) return '-'
    const d = new Date(date)
    return d.toLocaleDateString('de-DE', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
    })
  }

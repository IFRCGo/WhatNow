export const secondsToHms = (value) => {
  if (!value) return ''

  let sec = typeof value === 'string' ? parseInt(value, 10) : value
  sec = Math.floor(sec)
  const hours = Math.floor(sec / 3600)
  const minutes = Math.floor((sec - hours * 3600) / 60)
  const seconds = sec - hours * 3600 - minutes * 60

  if (hours === 0) {
    return minutes + ':' + ('0' + seconds).slice(-2) // Return in MM:SS format
  } else {
    return hours + ':' + ('0' + minutes).slice(-2) + ':' + ('0' + seconds).slice(-2) // Return in HH:MM:SS format
  }
}

export const formatDate = (date, format = 'YYYY-MM-DD') => {
  switch (format) {
    case 'YYYY-MM-DD':
      const month = date.getMonth() + 1
      const day = date.getDate()

      return `${date.getFullYear()}-${('0' + month).slice(-2)}-${('0' + day).slice(-2)}`
  }
}

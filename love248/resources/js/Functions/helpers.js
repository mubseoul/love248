/**
 * Format a date string to a human-readable format
 * @param {string} dateString - Date string in ISO format
 * @returns {string} - Formatted date string
 */
export function formatDate(dateString) {
  if (!dateString) return "N/A";

  const date = new Date(dateString);
  return date.toLocaleDateString(undefined, {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}

/**
 * Format a time string to a human-readable format with timezone conversion
 * @param {string} timeString - Time string in HH:MM format (assumed to be UTC)
 * @returns {string} - Formatted time string in user's local timezone
 */
export function formatTime(timeString) {
  if (!timeString) return "N/A";

  try {
    // Convert UTC time to user's local timezone
    const utcDateTime = new Date(`1970-01-01T${timeString}:00.000Z`);
    return utcDateTime.toLocaleTimeString(undefined, {
      hour: "numeric",
      minute: "2-digit",
      hour12: true,
    });
  } catch (error) {
    // Fallback to original format if conversion fails
    const [hours, minutes] = timeString.split(":").map(Number);
    const date = new Date();
    date.setHours(hours, minutes, 0);
    return date.toLocaleTimeString(undefined, {
      hour: "numeric",
      minute: "2-digit",
      hour12: true,
    });
  }
}

/**
 * Format a currency value
 * @param {number} amount - The amount to format
 * @param {string} currency - The currency code (default: USD)
 * @returns {string} - Formatted currency string
 */
export function formatCurrency(amount, currency = "USD") {
  return new Intl.NumberFormat(undefined, {
    style: "currency",
    currency,
  }).format(amount);
}

/**
 * Format a duration in minutes to a human-readable format
 * @param {number} minutes - Duration in minutes
 * @returns {string} - Formatted duration string
 */
export function formatDuration(minutes) {
  if (minutes < 60) {
    return `${minutes} min`;
  }

  const hours = Math.floor(minutes / 60);
  const remainingMinutes = minutes % 60;

  if (remainingMinutes === 0) {
    return `${hours} hr`;
  }

  return `${hours} hr ${remainingMinutes} min`;
}

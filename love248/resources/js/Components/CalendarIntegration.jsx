import React, { useState } from 'react';
import { MdEvent, MdCalendarToday, MdDownload, MdShare } from 'react-icons/md';
import __ from '@/Functions/Translate';

const CalendarIntegration = ({ streamRequest, isVisible = true }) => {
  const [isDropdownOpen, setIsDropdownOpen] = useState(false);

  if (!isVisible || !streamRequest) return null;

  // Debug logging to see what data we're receiving
  console.log('CalendarIntegration received streamRequest:', {
    requested_date: streamRequest.requested_date,
    requested_time: streamRequest.requested_time,
    duration_minutes: streamRequest.duration_minutes
  });

  // Format datetime for calendar
  const formatDateTime = (date, time) => {
    try {
      // Handle different date formats that might come from the backend
      let dateStr = date;
      if (typeof date === 'object' && date !== null) {
        // If date is an object (like from Laravel), convert to string
        dateStr = date.date || date;
      }
      
      // Create date string in a format that's consistently parseable
      const fullDateTimeString = `${dateStr} ${time}`;
      const streamDate = new Date(fullDateTimeString);
      
      // Check if the date is valid
      if (isNaN(streamDate.getTime())) {
        console.error('Invalid date created from:', { date, time, fullDateTimeString });
        return new Date(); // Return current date as fallback
      }
      
      return streamDate;
    } catch (error) {
      console.error('Error parsing date:', error, { date, time });
      return new Date(); // Return current date as fallback
    }
  };

  const streamStartTime = formatDateTime(streamRequest.requested_date, streamRequest.requested_time);
  const streamEndTime = new Date(streamStartTime.getTime() + (streamRequest.duration_minutes * 60000));

  // Format dates for different calendar systems
  const formatDateForCalendar = (date) => {
    return date.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';
  };

  // Generate calendar event details
  const getEventDetails = () => {
    const title = `Private Stream Session with ${streamRequest.streamer?.name || streamRequest.user?.name}`;
    const description = `
Private streaming session details:
• Duration: ${streamRequest.duration_minutes} minutes
• Streamer: ${streamRequest.streamer?.name || 'N/A'}
• User: ${streamRequest.user?.name || 'N/A'}
• Fee: $${streamRequest.streamer_fee}
• Status: ${streamRequest.status}

${streamRequest.message ? `Message: ${streamRequest.message}` : ''}

Join the session at: ${window.location.origin}/private-stream/session/${streamRequest.id}
    `.trim();

    const location = `${window.location.origin}/private-stream/session/${streamRequest.id}`;

    return {
      title,
      description,
      location,
      startTime: streamStartTime,
      endTime: streamEndTime
    };
  };

  // Generate Google Calendar URL
  const generateGoogleCalendarUrl = () => {
    const { title, description, location, startTime, endTime } = getEventDetails();
    
    const params = new URLSearchParams({
      action: 'TEMPLATE',
      text: title,
      dates: `${formatDateForCalendar(startTime)}/${formatDateForCalendar(endTime)}`,
      details: description,
      location: location,
      ctz: Intl.DateTimeFormat().resolvedOptions().timeZone
    });

    return `https://calendar.google.com/calendar/render?${params.toString()}`;
  };

  // Generate Outlook Calendar URL
  const generateOutlookCalendarUrl = () => {
    const { title, description, location, startTime, endTime } = getEventDetails();
    
    const params = new URLSearchParams({
      subject: title,
      startdt: startTime.toISOString(),
      enddt: endTime.toISOString(),
      body: description,
      location: location,
      allday: 'false',
      uid: `private-stream-${streamRequest.id}@${window.location.hostname}`
    });

    return `https://outlook.live.com/calendar/0/deeplink/compose?${params.toString()}`;
  };

  // Generate ICS file content
  const generateICSFile = () => {
    const { title, description, location, startTime, endTime } = getEventDetails();
    
    const icsContent = `BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Private Stream//Calendar Event//EN
CALSCALE:GREGORIAN
METHOD:PUBLISH
BEGIN:VEVENT
UID:private-stream-${streamRequest.id}@${window.location.hostname}
DTSTART:${formatDateForCalendar(startTime)}
DTEND:${formatDateForCalendar(endTime)}
SUMMARY:${title}
DESCRIPTION:${description.replace(/\n/g, '\\n')}
LOCATION:${location}
STATUS:CONFIRMED
SEQUENCE:0
BEGIN:VALARM
TRIGGER:-PT15M
ACTION:DISPLAY
DESCRIPTION:Private stream session starts in 15 minutes
END:VALARM
END:VEVENT
END:VCALENDAR`;

    return icsContent;
  };

  // Download ICS file
  const downloadICSFile = () => {
    // Use backend route for ICS generation
    const icsUrl = `/private-stream/${streamRequest.id}/calendar.ics`;
    
    const link = document.createElement('a');
    link.href = icsUrl;
    link.download = `private-stream-${streamRequest.id}.ics`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  // Open calendar in new tab
  const openCalendarLink = (url) => {
    window.open(url, '_blank', 'noopener,noreferrer');
    setIsDropdownOpen(false);
  };

  // Copy calendar link to clipboard
  const copyCalendarLink = async () => {
    const url = `${window.location.origin}/private-stream/session/${streamRequest.id}`;
    try {
      await navigator.clipboard.writeText(url);
      // You might want to show a toast notification here
      console.log('Calendar link copied to clipboard');
    } catch (err) {
      console.error('Failed to copy calendar link:', err);
    }
    setIsDropdownOpen(false);
  };

  return (
    <div className="relative">
      <button
        onClick={() => setIsDropdownOpen(!isDropdownOpen)}
        className="flex items-center space-x-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 text-sm"
        title={__('Add to Calendar')}
      >
        <MdEvent className="w-5 h-5" />
        <span className="hidden sm:inline">{__('Add to Calendar')}</span>
      </button>

      {/* Dropdown Menu */}
      {isDropdownOpen && (
        <>
          {/* Backdrop */}
          <div 
            className="fixed inset-0 z-10"
            onClick={() => setIsDropdownOpen(false)}
          />
          
          {/* Menu */}
          <div className="absolute top-full right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-20">
            <div className="p-3 border-b border-gray-200 dark:border-gray-700">
              <h4 className="font-medium text-gray-900 dark:text-white">
                {__('Add to Calendar')}
              </h4>
              <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {isNaN(streamStartTime.getTime()) ? 
                  'Date/Time TBD' : 
                  `${streamStartTime.toLocaleDateString()} at ${streamStartTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`
                }
              </p>
            </div>
            
            <div className="p-2">
              {/* Google Calendar */}
              <button
                onClick={() => openCalendarLink(generateGoogleCalendarUrl())}
                className="w-full flex items-center space-x-3 px-3 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors"
              >
                <div className="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                  <MdCalendarToday className="w-4 h-4 text-white" />
                </div>
                <span className="text-gray-900 dark:text-white">{__('Google Calendar')}</span>
              </button>

              {/* Outlook Calendar */}
              <button
                onClick={() => openCalendarLink(generateOutlookCalendarUrl())}
                className="w-full flex items-center space-x-3 px-3 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors"
              >
                <div className="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                  <MdCalendarToday className="w-4 h-4 text-white" />
                </div>
                <span className="text-gray-900 dark:text-white">{__('Outlook Calendar')}</span>
              </button>

              {/* Download ICS */}
              <button
                onClick={downloadICSFile}
                className="w-full flex items-center space-x-3 px-3 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors"
              >
                <div className="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                  <MdDownload className="w-4 h-4 text-white" />
                </div>
                <span className="text-gray-900 dark:text-white">{__('Download (.ics)')}</span>
              </button>

              {/* Copy Link */}
              <button
                onClick={copyCalendarLink}
                className="w-full flex items-center space-x-3 px-3 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors"
              >
                <div className="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center">
                  <MdShare className="w-4 h-4 text-white" />
                </div>
                <span className="text-gray-900 dark:text-white">{__('Copy Session Link')}</span>
              </button>
            </div>

            {/* Event Preview */}
            <div className="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
              <div className="text-xs text-gray-600 dark:text-gray-400">
                <div className="font-medium mb-1">{__('Event Details')}:</div>
                <div>• {__('Duration')}: {streamRequest.duration_minutes} {__('minutes')}</div>
                <div>• {__('Fee')}: ${streamRequest.streamer_fee}</div>
                <div>• {__('Reminder')}: 15 {__('minutes before')}</div>
              </div>
            </div>
          </div>
        </>
      )}
    </div>
  );
};

export default CalendarIntegration; 
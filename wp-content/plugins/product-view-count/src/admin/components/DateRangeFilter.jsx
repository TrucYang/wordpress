import React from 'react'
import DatePicker from 'react-datepicker'
import { subDays, subMonths } from 'date-fns'
import "react-datepicker/dist/react-datepicker.css"

const DateRangeFilter = ({ dateRange, onChange, loading }) => {
  const presetRanges = [
    {
      label: 'Last 7 days',
      range: {
        startDate: subDays(new Date(), 7),
        endDate: new Date()
      }
    },
    {
      label: 'Last 30 days',
      range: {
        startDate: subDays(new Date(), 30),
        endDate: new Date()
      }
    },
    {
      label: 'Last 3 months',
      range: {
        startDate: subMonths(new Date(), 3),
        endDate: new Date()
      }
    },
    {
      label: 'Last 6 months',
      range: {
        startDate: subMonths(new Date(), 6),
        endDate: new Date()
      }
    }
  ]

  const handlePresetClick = (preset) => {
    onChange(preset.range)
  }

  return (
    <div className="pvc-card">
      <h3 className="text-lg font-medium text-gray-900 mb-4">Date Range Filter</h3>
      
      <div className="flex flex-wrap items-center gap-4">
        <div className="flex items-center gap-2">
          <label className="text-sm font-medium text-gray-700">From:</label>
          <DatePicker
            selected={dateRange.startDate}
            onChange={(date) => onChange({ ...dateRange, startDate: date })}
            selectsStart
            startDate={dateRange.startDate}
            endDate={dateRange.endDate}
            maxDate={new Date()}
            className="pvc-input text-sm"
            disabled={loading}
          />
        </div>

        <div className="flex items-center gap-2">
          <label className="text-sm font-medium text-gray-700">To:</label>
          <DatePicker
            selected={dateRange.endDate}
            onChange={(date) => onChange({ ...dateRange, endDate: date })}
            selectsEnd
            startDate={dateRange.startDate}
            endDate={dateRange.endDate}
            minDate={dateRange.startDate}
            maxDate={new Date()}
            className="pvc-input text-sm"
            disabled={loading}
          />
        </div>

        <div className="flex items-center gap-2 ml-4">
          <span className="text-sm text-gray-600">Quick select:</span>
          {presetRanges.map((preset, index) => (
            <button
              key={index}
              onClick={() => handlePresetClick(preset)}
              className="pvc-button-secondary text-xs"
              disabled={loading}
            >
              {preset.label}
            </button>
          ))}
        </div>
      </div>
    </div>
  )
}

export default DateRangeFilter

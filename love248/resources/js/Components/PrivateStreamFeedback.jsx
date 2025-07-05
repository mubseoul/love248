import React, { useState } from 'react';
import __ from '@/Functions/Translate';
import { toast } from 'react-toastify';

export default function PrivateStreamFeedback({ streamRequest, isStreamer, onFeedbackSubmitted }) {
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [feedbackData, setFeedbackData] = useState({
        rating: 5,
        comment: '',
        user_showed_up: true,
        streamer_showed_up: true,
        technical_issues: false,
        technical_issues_description: '',
        inappropriate_behavior: false,
        inappropriate_behavior_description: '',
        overall_experience: 'good',
        would_recommend: true
    });

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsSubmitting(true);

        try {
            const response = await axios.post(
                route('private-stream.feedback.submit', streamRequest.id),
                feedbackData
            );

            if (response.data.status) {
                toast.success(response.data.message);
                if (onFeedbackSubmitted) {
                    onFeedbackSubmitted(response.data);
                }
            } else {
                toast.error(response.data.message);
            }
        } catch (error) {
            console.error('Error submitting feedback:', error);
            toast.error(__('Failed to submit feedback'));
        } finally {
            setIsSubmitting(false);
        }
    };

    const handleInputChange = (field, value) => {
        setFeedbackData(prev => ({
            ...prev,
            [field]: value
        }));
    };

    return (
        <div className="bg-footer border border-gray-600 rounded-xl shadow-xl p-6">
            <div className="text-center mb-6">
                <div className="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span className="text-2xl">📝</span>
                </div>
                <h3 className="text-2xl font-bold text-gray-primary mb-2">
                    {__('Stream Feedback')}
                </h3>
                <p className="text-gray-400">
                    {__('Help us improve by sharing your experience')}
                </p>
            </div>
            
            <form onSubmit={handleSubmit} className="space-y-6">
                {/* Rating */}
                <div className="bg-gray-800 p-4 rounded-lg">
                    <label className="block text-lg font-medium text-gray-primary mb-3 text-center">
                        {__('Overall Rating')}
                    </label>
                    <div className="flex justify-center space-x-1">
                        {[1, 2, 3, 4, 5].map(rating => (
                            <button
                                key={rating}
                                type="button"
                                onClick={() => handleInputChange('rating', rating)}
                                className={`text-4xl transition-all duration-200 hover:scale-110 ${
                                    rating <= feedbackData.rating 
                                        ? 'text-yellow-400 drop-shadow-lg' 
                                        : 'text-gray-500 hover:text-gray-400'
                                }`}
                            >
                                ★
                            </button>
                        ))}
                    </div>
                    <p className="text-center text-sm text-gray-400 mt-2">
                        {feedbackData.rating}/5 stars
                    </p>
                </div>

                {/* Comment */}
                <div>
                    <label className="block text-sm font-medium text-gray-primary mb-3">
                        {__('Comments')}
                    </label>
                    <textarea
                        value={feedbackData.comment}
                        onChange={(e) => handleInputChange('comment', e.target.value)}
                        className="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-gray-primary focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200"
                        rows="4"
                        placeholder={__('Share your experience...')}
                        maxLength="1000"
                    />
                    <div className="text-right text-xs text-gray-500 mt-1">
                        {feedbackData.comment.length}/1000
                    </div>
                </div>

                {/* Attendance */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-primary mb-2">
                            {isStreamer ? __('User showed up') : __('Streamer showed up')}
                        </label>
                        <select
                            value={isStreamer ? feedbackData.user_showed_up : feedbackData.streamer_showed_up}
                            onChange={(e) => handleInputChange(
                                isStreamer ? 'user_showed_up' : 'streamer_showed_up', 
                                e.target.value === 'true'
                            )}
                            className="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-gray-primary focus:outline-none focus:border-primary"
                        >
                            <option value="true">{__('Yes')}</option>
                            <option value="false">{__('No')}</option>
                        </select>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-primary mb-2">
                            {__('Overall Experience')}
                        </label>
                        <select
                            value={feedbackData.overall_experience}
                            onChange={(e) => handleInputChange('overall_experience', e.target.value)}
                            className="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-gray-primary focus:outline-none focus:border-primary"
                        >
                            <option value="excellent">{__('Excellent')}</option>
                            <option value="good">{__('Good')}</option>
                            <option value="average">{__('Average')}</option>
                            <option value="poor">{__('Poor')}</option>
                            <option value="terrible">{__('Terrible')}</option>
                        </select>
                    </div>
                </div>

                {/* Issues */}
                <div className="space-y-3">
                    <div>
                        <label className="flex items-center">
                            <input
                                type="checkbox"
                                checked={feedbackData.technical_issues}
                                onChange={(e) => handleInputChange('technical_issues', e.target.checked)}
                                className="mr-2"
                            />
                            <span className="text-gray-primary">{__('Technical Issues')}</span>
                        </label>
                        {feedbackData.technical_issues && (
                            <textarea
                                value={feedbackData.technical_issues_description}
                                onChange={(e) => handleInputChange('technical_issues_description', e.target.value)}
                                className="w-full mt-2 px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-gray-primary focus:outline-none focus:border-primary"
                                rows="2"
                                placeholder={__('Describe the technical issues...')}
                                maxLength="500"
                            />
                        )}
                    </div>

                    <div>
                        <label className="flex items-center">
                            <input
                                type="checkbox"
                                checked={feedbackData.inappropriate_behavior}
                                onChange={(e) => handleInputChange('inappropriate_behavior', e.target.checked)}
                                className="mr-2"
                            />
                            <span className="text-gray-primary">{__('Inappropriate Behavior')}</span>
                        </label>
                        {feedbackData.inappropriate_behavior && (
                            <textarea
                                value={feedbackData.inappropriate_behavior_description}
                                onChange={(e) => handleInputChange('inappropriate_behavior_description', e.target.value)}
                                className="w-full mt-2 px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-gray-primary focus:outline-none focus:border-primary"
                                rows="2"
                                placeholder={__('Describe the inappropriate behavior...')}
                                maxLength="500"
                            />
                        )}
                    </div>
                </div>

                {/* Recommendation */}
                <div>
                    <label className="block text-sm font-medium text-gray-primary mb-2">
                        {isStreamer 
                            ? __('Would you recommend this user to other streamers?')
                            : __('Would you recommend this streamer to others?')
                        }
                    </label>
                    <div className="flex space-x-4">
                        <label className="flex items-center">
                            <input
                                type="radio"
                                name="would_recommend"
                                checked={feedbackData.would_recommend === true}
                                onChange={() => handleInputChange('would_recommend', true)}
                                className="mr-2"
                            />
                            <span className="text-gray-primary">{__('Yes')}</span>
                        </label>
                        <label className="flex items-center">
                            <input
                                type="radio"
                                name="would_recommend"
                                checked={feedbackData.would_recommend === false}
                                onChange={() => handleInputChange('would_recommend', false)}
                                className="mr-2"
                            />
                            <span className="text-gray-primary">{__('No')}</span>
                        </label>
                    </div>
                </div>

                {/* Submit Button */}
                <div className="pt-4 border-t border-gray-600">
                    <button
                        type="submit"
                        disabled={isSubmitting}
                        className="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-semibold text-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:from-blue-600 disabled:hover:to-blue-700"
                    >
                        {isSubmitting ? (
                            <span className="flex items-center justify-center">
                                <div className="animate-spin rounded-full h-5 w-5 border-t-2 border-b-2 border-white mr-2"></div>
                                {__('Submitting...')}
                            </span>
                        ) : (
                            __('Submit Feedback')
                        )}
                    </button>
                </div>
            </form>
        </div>
    );
} 
// resources/js/reducers/index.js
import { combineReducers } from 'redux';
import SwiperReducer from './SwiperReducer';

const rootReducer = combineReducers({
    swiper: SwiperReducer,
});

export default rootReducer;

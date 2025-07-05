// resources/js/reducers/exampleReducer.js
import { createSlice } from '@reduxjs/toolkit';

const SwiperReducer = createSlice({
    name: 'Swiper',
    initialState: {
        value: "ltr",
    },
    reducers: {
        increment: (state) => {
            state.value += 1;
        },
        decrement: (state) => {
            state.value -= 1;
        },
    },
});

export const { increment, decrement } = SwiperReducer.actions;

export default SwiperReducer.reducer;

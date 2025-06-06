@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-tr from-amber-50/20 to-stone-50 py-4 px-4 sm:px-6 lg:px-8" x-data="rateChecker()">
        <div class="max-w-8xl mx-auto  p-8 md:p-10 space-y-8 ">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-x-4 gap-y-8">
                <div class="rounded-xl p-12 bg-white border lg:col-span-2 border-gray-200 lg:pr-8 space-y-7">
                    <h2 class="text-2xl font-bold text-gray-800 mb-5 border-b pb-3">Get a Quote for Your Next Adventure</h2>
                    @csrf
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">Select Unit Type</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <template x-for="unit in units" :key="unit.name">
                                    <div @click="form['Unit Name'] = unit.name"
                                        :class="{
                                            'border-yellow-500 border bg-yellow-50 text-yellow-600': form['Unit Name'] === unit.name,
                                            'border-gray-300': form['Unit Name'] !== unit.name
                                        }"
                                        class="relative group overflow-hidden rounded-xl cursor-pointer transition-all duration-300 ease-in-out">

                                        <div class="relative h-48 w-full">
                                            <img :src="unit.image" :alt="unit.name"
                                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">

                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                <div
                                                    class="absolute bottom-0 left-0 right-0 p-4 text-white backdrop-blur-sm bg-white/10">
                                                    <p class="text-sm line-clamp-3" x-text="unit.description"></p>
                                                </div>
                                            </div>

                                            <div x-show="form['Unit Name'] === unit.name"
                                                class="absolute top-3 right-3 bg-yellow-500 text-white rounded-full p-2 shadow-lg z-10">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="p-4 group-hover:bg-yellow-50 transition-all duration-300">
                                            <h3 class="text-lg font-semibold  group-hover:text-yellow-600 transition-colors duration-300 flex items-center justify-between">
                                                <span x-text="unit.name"></span>

                                            </h3>

                                        </div>

                                    </div>
                                </template>
                            </div>
                            <p x-show="!form['Unit Name'] && submitted" class="text-xs text-red-500 mt-1.5 animate-pulse">
                                Please select a unit.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="col-span-2">
                                <label for="datepicker" class="block text-sm font-semibold text-gray-800 mb-2">
                                    Select Stay Dates
                                </label>
                                <div class="relative" @keydown.escape="closeDatepicker()" @click.outside="closeDatepicker()">
                                    <div class="inline-flex items-center border border-gray-300 rounded-lg border bg-white w-full">
                                        <input type="text" @click="endToShow = 'from'; initDatepicker(); showDatepicker = true"
                                            x-model="outputDateFromValue" :class="{ 'font-semibold': endToShow == 'from' }"
                                            class="focus:outline-none border-0 p-2.5 w-full rounded-l-lg border-r border-gray-300" />
                                        <div class="inline-block px-3 h-full text-gray-500">to</div>
                                        <input type="text" @click="endToShow = 'to'; initDatepicker(); showDatepicker = true"
                                            x-model="outputDateToValue" :class="{ 'font-semibold': endToShow == 'to' }"
                                            class="focus:outline-none border-0 p-2.5 w-full rounded-r-lg border-l border-gray-300" />

                                    </div>
                                    <div x-show="(!form.Arrival || !form.Departure) && submitted" class="mt-1.5">
                                        <p x-show="!form.Arrival" class="text-xs text-red-500 animate-pulse">Arrival date required.</p>
                                        <p x-show="!form.Departure" class="text-xs text-red-500 animate-pulse">Departure date required.</p>

                                    </div>
                                     <p class="text-xs text-gray-500 mt-1.5">How long do you plan on enjoying yourself?</p>

                                    <div class="bg-white mt-2 rounded-lg border p-4 absolute z-10" style="width: 17rem"
                                        x-show="showDatepicker" x-transition>
                                        <div class="flex flex-col items-center">
                                            <div class="w-full flex justify-between items-center mb-2">
                                                <div>
                                                    <span x-text="MONTH_NAMES[month]" class="text-lg font-bold text-gray-800"></span>
                                                    <span x-text="year" class="ml-1 text-lg text-gray-600 font-normal"></span>
                                                </div>
                                                <div>
                                                    <button type="button" class="transition ease-in-out duration-100 inline-flex cursor-pointer hover:bg-gray-200 p-1 rounded-full"
                                                        @click="if (month == 0) {year--; month=11;} else {month--;} getNoOfDays()">
                                                        <svg class="h-6 w-6 text-gray-500 inline-flex" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                                        </svg>
                                                    </button>
                                                    <button type="button" class="transition ease-in-out duration-100 inline-flex cursor-pointer hover:bg-gray-200 p-1 rounded-full"
                                                        @click="if (month == 11) {year++; month=0;} else {month++;}; getNoOfDays()">
                                                        <svg class="h-6 w-6 text-gray-500 inline-flex" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="w-full flex flex-wrap mb-3 -mx-1">
                                                <template x-for="(day, index) in DAYS" :key="index">
                                                    <div style="width: 14.26%" class="px-1">
                                                        <div x-text="day" class="text-gray-800 font-medium text-center text-xs"></div>
                                                    </div>
                                                </template>
                                            </div>

                                            <div class="flex flex-wrap -mx-1">
                                                <template x-for="blankday in blankdays">
                                                    <div style="width: 14.28%" class="text-center border p-1 border-transparent text-sm"></div>
                                                </template>
                                                <template x-for="(date, dateIndex) in no_of_days" :key="dateIndex">
                                                    <div style="width: 14.28%">
                                                        <div @click="getDateValue(date)" x-text="date"
                                                            class="p-1 cursor-pointer text-center text-sm leading-none leading-loose transition ease-in-out duration-100"
                                                            :class="{
                                                                'font-bold': isToday(date) == true,
                                                                'bg-yellow-800 text-white rounded-l-full': isDateFrom(date) == true,
                                                                'bg-yellow-800 text-white rounded-r-full': isDateTo(date) == true,
                                                                'bg-yellow-200': isInRange(date) == true,
                                                                'text-gray-400 cursor-not-allowed': isPastDate(date)
                                                            }"
                                                            :disabled="isPastDate(date)">
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="occupants" class="block text-sm font-semibold text-gray-800 mb-2">
                                    Number of Guests
                                </label>
                                <input id="occupants" x-model.number="form.Occupants" type="number" min="1" max="10"
                                    @input="updateAgesFields"
                                    class="form-input w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500 text-base appearance-none transition-all duration-200 ease-in-out">
                                <p class="text-xs text-gray-500 mt-1.5">Total count, including all adults and children.</p>
                                <p x-show="form.Occupants < 1 && submitted" class="text-xs text-red-500 mt-1.5 animate-pulse">
                                    At least 1 guest is required.
                                </p>
                            </div>
                        </div>




                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <template x-for="(age, index) in form.Ages" :key="index">
                                <div>
                                    <label :for="'age-' + index"
                                        class="block text-sm font-semibold text-gray-800 mb-2">Age of Guest <span
                                            x-text="index + 1"></span></label>
                                    <input :id="'age-' + index" x-model.number="form.Ages[index]" type="number"
                                        min="0"
                                        class="form-input w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500 text-base transition-all duration-200 ease-in-out"
                                        placeholder="e.g., 5, 12, 30">
                                    <p x-show="(form.Ages[index] === null || isNaN(form.Ages[index]) || form.Ages[index] < 0) && submitted"
                                        class="text-xs text-red-500 mt-1.5 animate-pulse">Please enter a valid age.</p>
                                </div>
                            </template>
                        </div>



                        <div class="pt-4">
                            <button type="submit"
                                class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-3.5 rounded-lg border hover:border transition transform hover:-translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 cursor-pointer"
                                :disabled="loading || !allFieldsValid()">
                                <template x-if="loading">
                                    <span class="flex justify-center items-center space-x-2">
                                        <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4" fill="none"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                        </svg>
                                        <span>Checking Rates...</span>
                                    </span>
                                </template>
                                <span x-show="!loading">Check Rates</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="space-y-7 lg:pl-8 rounded-xl p-12 bg-white border border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-5 border-b pb-3">Dream Trip Details</h2>

                    <template x-if="error">
                        <div class="bg-red-50 border border-red-300 text-red-700 px-5 py-4 rounded-lg relative border animate-fade-in-down"
                            role="alert">
                            <strong class="font-semibold text-lg">Error:</strong>
                            <span class="block sm:inline ml-2" x-text="error"></span>
                            <button type="button"
                                class="absolute top-3 right-3 text-red-500 hover:text-red-700 focus:outline-none"
                                @click="error = null">
                                <svg class="fill-current h-6 w-6" role="button" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <title>Close</title>
                                    <path
                                        d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.104l-2.651 2.651a1.2 1.2 0 1 1-1.697-1.697L8.303 9.407l-2.651-2.651a1.2 1.2 0 1 1 1.697-1.697L10 7.713l2.651-2.651a1.2 1.2 0 0 1 1.697 1.697L11.697 9.407l2.651 2.651a1.2 1.2 0 0 1 0 1.697z" />
                                </svg>
                            </button>
                        </div>
                    </template>

                    <template x-if="result && result.remote_response">
                        <div class="bg-white  p-2 rounded-lg">
                            <div class="mb-6">
                                <h3 class="font-semibold text-xl text-gray-900 mb-4">Rate Details</h3>
                                <div class="flex justify-between items-center mb-4 pb-3 border-b">
                                    <p class="text-md font-semibold text-gray-900">Total Amount</p>
                                    <p class="text-md font-semibold text-gray-900 truncate"
                                        x-text="formatCurrency(result.remote_response['Total Charge'])"></p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Booking Reference</p>
                                        <p class="font-medium text-gray-900 truncate"
                                            x-text="result.remote_response['Booking Group ID']"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Number of Rooms</p>
                                        <p class="font-medium text-gray-900">
                                            <span x-show="result.remote_response.Rooms > 0"
                                                x-text="result.remote_response.Rooms"></span>
                                            <span x-show="result.remote_response.Rooms === 0"
                                                class="text-red-500 font-medium">No rooms available</span>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Extra Charges</p>
                                        <p class="font-medium text-gray-900"
                                            x-text="formatCurrency(result.remote_response['Extras Charge'])"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-6" x-show="result.remote_response.your_guest_breakdown">
                                <h4 class="text-md font-semibold text-gray-900 mb-3">Guest Information</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <template x-for="(guest, index) in result.remote_response.your_guest_breakdown"
                                        :key="index">
                                        <div class="border border-gray-100 p-3 rounded"
                                            :class="{ 'bg-yellow-50': guest.age > 13, 'bg-green-50': guest.age <= 13 }">
                                            <p class="text-xs text-gray-500">Guest <span x-text="index + 1"></span></p>
                                            <p class="font-medium text-sm text-gray-900">
                                                <span x-text="guest.age + ' years old'"></span>
                                                <span x-text="guest.age <= 13 ? ' (Child)' : ' (Adult)'"
                                                    class="text-xs"></span>
                                            </p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="mb-6"
                                x-show="result.remote_response.Legs && result.remote_response.Legs.length > 0">
                                <h4 class="text-md font-semibold text-gray-900 mb-3">Rate Details</h4>
                                <div class="space-y-3">
                                    <template x-for="(rate, index) in result.remote_response.Legs" :key="index">
                                        <div class="border border-gray-100 p-4 rounded bg-gray-50">
                                            <p class="font-medium text-gray-900 mb-3"
                                                x-text="rate['Special Rate Description']"></p>

                                            <div class="grid grid-cols-2 gap-4 mb-3">
                                                <div>
                                                    <p class="text-sm text-gray-500">Daily Rate</p>
                                                    <p class="font-medium text-gray-900"
                                                        x-text="formatCurrency(rate['Effective Average Daily Rate'])"></p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-500">Total Charge</p>
                                                    <p class="font-medium text-gray-900"
                                                        x-text="formatCurrency(rate['Total Charge'])"></p>
                                                </div>
                                            </div>

                                            <div class="mt-3"
                                                x-show="rate['Deposit Breakdown'] && rate['Deposit Breakdown'].length > 0">
                                                <p class="text-sm font-semibold text-gray-700 mb-2">Deposit Information</p>
                                                <template x-for="(deposit, dIndex) in rate['Deposit Breakdown']"
                                                    :key="dIndex">
                                                    <div class="flex justify-between items-center text-sm">
                                                        <span x-text="'Due: ' + deposit['Due Date Formatted']"></span>
                                                        <span class="font-medium"
                                                            x-text="formatCurrency(deposit['Due Amount'])"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                </div>
                </template>

                <template x-if="!result && !error">
                    <div
                        class="bg-gray-100 border border-gray-300 text-gray-600 px-4 py-4 rounded-lg relative border text-center text-lg">
                        <p class="mb-2">Rates will appear here after you submit the form.</p>
                        <p class="text-sm">Fill in the details on the left to get started!</p>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        const MONTH_NAMES = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September',
            'October', 'November', 'December'
        ];
        const DAYS = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        document.addEventListener('alpine:init', () => {
            Alpine.data('rateChecker', () => ({
            form: {
                'Unit Name': '',
                'Arrival': '',
                'Departure': '',
                'Occupants': 1,
                'Ages': [0]
            },
            loading: false,
            result: null,
            error: null,
            submitted: false,

            units: [{
                name: 'Kalahari Farmhouse',
                image: 'https://travelground.imgix.net/AAEAAQAAAAAAAAAAAAAAd9ba2af10e20060dc151da5d7b3b00d6dbf5f77d283c33396fafb5fd7a9fe9e6e3b42aa098e2ef07d44359e00a692e964a07?fit=crop&auto=enhance,format,compress&q=80&w=1600&ar=1:1',
                description: 'Resting at the fringes of the Kalahari Farmhouse, in a grove of palm trees, Kalahari Farmhouse Campsite is an unexpected find in the Kalahari. Blessed with artesian water, the small pocket of land is a verdant oasis that produces an abundance of produce for the Gondwana Collection.'
                },
                {
                name: 'Klipspringer Camps',
                image: 'https://www.namibia-forum.ch/images/obgrabber/2020-06/0e5c0537fc.jpeg',
                description: 'Scenic camping sites situated among rocky outcrops, perfect for nature lovers seeking an intimate desert experience with basic amenities.'
                },
            ],

            showDatepicker: false,
            outputDateFromValue: '',
            outputDateToValue: '',
            dateFrom: null,
            dateTo: null,
            currentDate: null,
            endToShow: '',

            month: '',
            year: '',
            no_of_days: [],
            blankdays: [],

            init() {
                this.currentDate = new Date();
                this.month = this.currentDate.getMonth();
                this.year = this.currentDate.getFullYear();
                this.getNoOfDays();
                this.updateAgesFields();

                if (this.form.Arrival) {
                this.dateFrom = this.parseDateForValidation(this.form.Arrival);
                this.outputDateFromValue = this.form.Arrival;
                }
                if (this.form.Departure) {
                this.dateTo = this.parseDateForValidation(this.form.Departure);
                this.outputDateToValue = this.form.Departure;
                }
            },

            parseDateForValidation(dateString) {
                if (!dateString) return null;
                const parts = dateString.split('/');
                if (parts.length === 3) {
                const d = new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[
                    0]));
                d.setHours(0, 0, 0, 0);
                return d;
                }
                return null;
            },

            convertDmYToYmd(dateString) {
                if (!dateString) return '';
                const parts = dateString.split('/');
                if (parts.length === 3) {
                return `${parts[2]}-${parts[1]}-${parts[0]}`;
                }
                return dateString;
            },

            formatDateForDisplay(dateYmdString) {
                if (!dateYmdString) return '';
                const parts = dateYmdString.split('-');
                if (parts.length === 3) {
                return `${parts[2]}/${parts[1]}/${parts[0]}`;
                }
                return dateYmdString;
            },

            initDatepicker() {
                let targetDate = new Date();

                if (this.endToShow === 'from' && this.dateFrom) {
                targetDate = this.dateFrom;
                } else if (this.endToShow === 'to' && this.dateTo) {
                targetDate = this.dateTo;
                } else if (this.dateFrom) {
                targetDate = this.dateFrom;
                } else if (this.dateTo) {
                targetDate = this.dateTo;
                }

                this.month = targetDate.getMonth();
                this.year = targetDate.getFullYear();
                this.getNoOfDays();
            },

            isToday(date) {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const d = new Date(this.year, this.month, date);
                d.setHours(0, 0, 0, 0);
                return today.toDateString() === d.toDateString();
            },

            isPastDate(date) {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const d = new Date(this.year, this.month, date);
                d.setHours(0, 0, 0, 0);
                return d < today;
            },

            isDateFrom(date) {
                const d = new Date(this.year, this.month, date);
                d.setHours(0, 0, 0, 0);
                return this.dateFrom && d.toDateString() === this.dateFrom.toDateString();
            },

            isDateTo(date) {
                const d = new Date(this.year, this.month, date);
                d.setHours(0, 0, 0, 0);
                return this.dateTo && d.toDateString() === this.dateTo.toDateString();
            },

            isInRange(date) {
                const d = new Date(this.year, this.month, date);
                d.setHours(0, 0, 0, 0);

                if (!this.dateFrom) return false;

                let effectiveDateFrom = this.dateFrom;
                let effectiveDateTo = this.dateTo;

                if (!effectiveDateTo || (this.endToShow === 'to' && d < effectiveDateFrom)) {
                effectiveDateTo = d;
                }

                if (effectiveDateFrom > effectiveDateTo) {
                [effectiveDateFrom, effectiveDateTo] = [effectiveDateTo, effectiveDateFrom];
                }

                return d > effectiveDateFrom && d < effectiveDateTo;
            },

            getDateValue(date) {
                if (this.isPastDate(date)) {
                return;
                }

                let selectedDate = new Date(this.year, this.month, date);
                selectedDate.setHours(0, 0, 0, 0);

                if (!this.dateFrom || (this.dateFrom && this.dateTo)) {
                this.dateFrom = selectedDate;
                this.dateTo = null;
                this.endToShow = 'to';
                } else if (this.dateFrom && !this.dateTo) {
                if (selectedDate <= this.dateFrom) {
                    this.dateTo = this.dateFrom;
                    this.dateFrom = selectedDate;
                } else {
                    this.dateTo = selectedDate;
                }
                this.closeDatepicker();
                }
                this.outputDateValues();
            },

            outputDateValues() {
                if (this.dateFrom) {
                this.outputDateFromValue = this.dateFrom.toLocaleDateString('en-GB');
                this.form.Arrival = this.outputDateFromValue;
                } else {
                this.outputDateFromValue = '';
                this.form.Arrival = '';
                }

                if (this.dateTo) {
                this.outputDateToValue = this.dateTo.toLocaleDateString('en-GB');
                this.form.Departure = this.outputDateToValue;
                } else {
                this.outputDateToValue = '';
                this.form.Departure = '';
                }
            },

            getNoOfDays() {
                let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
                let dayOfWeek = new Date(this.year, this.month, 1).getDay();

                let blankdaysArray = [];
                for (let i = 0; i < dayOfWeek; i++) {
                blankdaysArray.push(i);
                }

                let daysArray = [];
                for (let i = 1; i <= daysInMonth; i++) {
                daysArray.push(i);
                }

                this.blankdays = blankdaysArray;
                this.no_of_days = daysArray;
            },

            closeDatepicker() {
                this.showDatepicker = false;
            },

            updateAgesFields() {
                const currentAgesCount = this.form.Ages.length;
                const newOccupants = Math.max(1, Math.min(10, parseInt(this.form.Occupants) || 1));

                if (newOccupants > currentAgesCount) {
                for (let i = currentAgesCount; i < newOccupants; i++) {
                    this.form.Ages.push(0);
                }
                } else if (newOccupants < currentAgesCount) {
                this.form.Ages = this.form.Ages.slice(0, newOccupants);
                }
            },

            allFieldsValid() {
                if (!this.form['Unit Name'] || !this.form.Arrival || !this.form.Departure) {
                return false;
                }
                const arrivalDate = this.parseDateForValidation(this.form.Arrival);
                const departureDate = this.parseDateForValidation(this.form.Departure);

                if (!arrivalDate || !departureDate || arrivalDate >= departureDate) {
                return false;
                }

                if (this.form.Occupants < 1 || this.form.Occupants > 10) {
                return false;
                }
                for (let i = 0; i < this.form.Ages.length; i++) {
                const age = this.form.Ages[i];
                if (age === null || isNaN(age) || age < 0) {
                    return false;
                }
                }
                return true;
            },

            async submitForm() {
                this.submitted = true;
                this.error = null;
                this.result = null;

                if (!this.allFieldsValid()) {
                this.error = "Please fill in all required fields and ensure dates are valid.";
                return;
                }

                this.loading = true;

                const payload = {
                'Unit Name': this.form['Unit Name'],
                'Arrival': this.form.Arrival,
                'Departure': this.form.Departure,
                'Occupants': this.form.Occupants,
                'Ages': this.form.Ages
                };
                console.log('Full Payload sent to API:', payload);
                const apiUrl = 'api/get-rates';

                try {
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    if (errorData.errors) {
                    let detailedError = "Validation Errors:<br>";
                    for (const key in errorData.errors) {
                        detailedError += `- ${errorData.errors[key].join(', ')}<br>`;
                    }
                    throw new Error(detailedError);
                    } else {
                    throw new Error(errorData.message || `API error: ${response.statusText}`);
                    }
                }

                const responseData = await response.json();

                if (responseData.success === false) {
                    this.error = responseData.message || "An unknown error occurred.";
                } else {
                    this.result = responseData;
                    console.log(responseData)
                }

                } catch (e) {
                console.error("API Error:", e);
                this.error = e.message || "Failed to fetch rates. Please check your network connection or the API endpoint.";
                } finally {
                this.loading = false;
                }
            },

            formatCurrency(amount) {
                return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'NAD'
                }).format(amount);
            }
            }));
        });
    </script>
@endsection

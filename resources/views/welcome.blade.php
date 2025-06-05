@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-4 px-4 sm:px-6 lg:px-8" x-data="rateChecker()">
        <div class="max-w-6xl mx-auto bg-white border rounded-xl p-8 md:p-10 space-y-8 border border-gray-200">
            <h1 class="text-4xl font-extrabold text-gray-900 text-center leading-tight">
                <span class="inline-block transform -rotate-3 text-blue-600 mr-2">✨</span> Get Your Stay Rates
            </h1>
            <p class="text-center text-gray-600 text-lg mb-6">Enter your details to receive an instant price estimate.</p>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-x-4 gap-y-8">
                <div class="lg:border-r lg:col-span-2 lg:border-gray-200 lg:pr-8 space-y-7">
                    <h2 class="text-2xl font-bold text-gray-800 mb-5 border-b pb-3">Booking Details</h2>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">Select Unit Type</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <template x-for="unit in units" :key="unit.name">
                                    <div @click="form['Unit Name'] = unit.name"
                                        :class="{ 'border-blue-500 scale-102': form['Unit Name'] === unit
                                            .name, 'border-gray-300 hover:border-blue-300': form['Unit Name'] !== unit
                                                .name }"
                                        class="relative flex flex-col items-center justify-center p-2 border rounded-lg border cursor-pointer transition-all duration-200 ease-in-out group">
                                        <img :src="unit.image" :alt="unit.name"
                                            class="w-full h-32 object-cover rounded-md mb-3 group-hover:scale-102 transition-transform duration-200 ease-in-out">
                                        <span class="text-lg font-medium text-gray-800 group-hover:text-blue-600"
                                            x-text="unit.name"></span>
                                        <template x-if="form['Unit Name'] === unit.name">
                                            <div
                                                class="absolute top-2 right-2 bg-blue-500 text-white rounded-full p-1.5 border">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                            <p x-show="!form['Unit Name'] && submitted" class="text-xs text-red-500 mt-1.5 animate-pulse">
                                Please select a unit.</p>
                        </div>

                        <div>
                            <label for="datepicker" class="block text-sm font-semibold text-gray-800 mb-2">Select Stay
                                Dates</label>
                            <div class="relative" @keydown.escape="closeDatepicker()" @click.outside="closeDatepicker()">
                                <div
                                    class="inline-flex items-center border border-gray-300 rounded-lg border bg-white w-full">
                                    <input type="text"
                                        @click="endToShow = 'from'; initDatepicker(); showDatepicker = true"
                                        x-model="outputDateFromValue" :class="{ 'font-semibold': endToShow == 'from' }"
                                        class="focus:outline-none border-0 p-2.5 w-full rounded-l-lg border-r border-gray-300" />
                                    <div class="inline-block px-3 h-full text-gray-500">to</div>
                                    <input type="text" @click="endToShow = 'to'; initDatepicker(); showDatepicker = true"
                                        x-model="outputDateToValue" :class="{ 'font-semibold': endToShow == 'to' }"
                                        class="focus:outline-none border-0 p-2.5 w-full rounded-r-lg border-l border-gray-300" />
                                </div>
                                <p x-show="!form.Arrival && submitted" class="text-xs text-red-500 mt-1.5 animate-pulse">
                                    Arrival date is required.</p>
                                <p x-show="!form.Departure && submitted" class="text-xs text-red-500 mt-1.5 animate-pulse">
                                    Departure date is required.</p>


                                <div class="bg-white mt-2 rounded-lg border p-4 absolute z-10" style="width: 17rem"
                                    x-show="showDatepicker" x-transition>
                                    <div class="flex flex-col items-center">

                                        <div class="w-full flex justify-between items-center mb-2">
                                            <div>
                                                <span x-text="MONTH_NAMES[month]"
                                                    class="text-lg font-bold text-gray-800"></span>
                                                <span x-text="year" class="ml-1 text-lg text-gray-600 font-normal"></span>
                                            </div>
                                            <div>
                                                <button type="button"
                                                    class="transition ease-in-out duration-100 inline-flex cursor-pointer hover:bg-gray-200 p-1 rounded-full"
                                                    @click="if (month == 0) {year--; month=11;} else {month--;} getNoOfDays()">
                                                    <svg class="h-6 w-6 text-gray-500 inline-flex" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 19l-7-7 7-7" />
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    class="transition ease-in-out duration-100 inline-flex cursor-pointer hover:bg-gray-200 p-1 rounded-full"
                                                    @click="if (month == 11) {year++; month=0;} else {month++;}; getNoOfDays()">
                                                    <svg class="h-6 w-6 text-gray-500 inline-flex" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="w-full flex flex-wrap mb-3 -mx-1">
                                            <template x-for="(day, index) in DAYS" :key="index">
                                                <div style="width: 14.26%" class="px-1">
                                                    <div x-text="day"
                                                        class="text-gray-800 font-medium text-center text-xs"></div>
                                                </div>
                                            </template>
                                        </div>

                                        <div class="flex flex-wrap -mx-1">
                                            <template x-for="blankday in blankdays">
                                                <div style="width: 14.28%"
                                                    class="text-center border p-1 border-transparent text-sm"></div>
                                            </template>
                                            <template x-for="(date, dateIndex) in no_of_days" :key="dateIndex">
                                                <div style="width: 14.28%">
                                                    <div @click="getDateValue(date)" x-text="date"
                                                        class="p-1 cursor-pointer text-center text-sm leading-none leading-loose transition ease-in-out duration-100"
                                                        :class="{
                                                            'font-bold': isToday(date) == true,
                                                            'bg-blue-800 text-white rounded-l-full': isDateFrom(date) ==
                                                                true,
                                                            'bg-blue-800 text-white rounded-r-full': isDateTo(date) ==
                                                                true,
                                                            'bg-blue-200': isInRange(date) == true,
                                                            'text-gray-400 cursor-not-allowed': isPastDate(
                                                                date) // Disable past dates
                                                        }"
                                                        :disabled="isPastDate(date)"></div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div>
                            <label for="occupants" class="block text-sm font-semibold text-gray-800 mb-2">Number of
                                Guests</label>
                            <input id="occupants" x-model.number="form.Occupants" type="number" min="1"
                                max="10" @input="updateAgesFields"
                                class="form-input w-full px-4 py-2.5 border border-gray-300 rounded-lg border focus:ring-blue-500 focus:border-blue-500 text-base appearance-none transition-all duration-200 ease-in-out">
                            <p class="text-xs text-gray-500 mt-1.5">Total count, including all adults and children.</p>
                            <p x-show="form.Occupants < 1 && submitted" class="text-xs text-red-500 mt-1.5 animate-pulse">
                                At least 1 guest is required.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <template x-for="(age, index) in form.Ages" :key="index">
                                <div>
                                    <label :for="'age-' + index"
                                        class="block text-sm font-semibold text-gray-800 mb-2">Age of Guest <span
                                            x-text="index + 1"></span></label>
                                    <input :id="'age-' + index" x-model.number="form.Ages[index]" type="number"
                                        min="0"
                                        class="form-input w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-base transition-all duration-200 ease-in-out"
                                        placeholder="e.g., 5, 12, 30">
                                    <p x-show="(form.Ages[index] === null || isNaN(form.Ages[index]) || form.Ages[index] < 0) && submitted"
                                        class="text-xs text-red-500 mt-1.5 animate-pulse">Please enter a valid age.</p>
                                </div>
                            </template>
                        </div>



                        <div class="pt-4">
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-lg border hover:border transition transform hover:-translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
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

                <div class="space-y-7 lg:pl-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-5 border-b pb-3">Your Results</h2>

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
                        <div class="bg-white border border-gray-200 p-2 rounded-lg">
                            <!-- Main Summary -->
                            <div class="mb-6">
                                <h3 class="font-semibold text-xl text-gray-900 mb-4">Rate Details</h3>
                                <div class="flex justify-between items-center mb-4 pb-3 border-b">
                                    <p class="text-md font-semibold text-gray-900">Total Amount</p>
                                    <p class="text-md font-semibold text-gray-900 truncate" x-text="formatCurrency(result.remote_response['Total Charge'])"></p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Booking Reference</p>
                                        <p class="font-medium text-gray-900 truncate" x-text="result.remote_response['Booking Group ID']"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Number of Rooms</p>
                                        <p class="font-medium text-gray-900" x-text="result.remote_response.Rooms"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Extra Charges</p>
                                        <p class="font-medium text-gray-900" x-text="formatCurrency(result.remote_response['Extras Charge'])"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Guest Information -->
                            <div class="mb-6" x-show="result.remote_response.your_guest_breakdown">
                                <h4 class="text-md font-semibold text-gray-900 mb-3">Guest Information</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <template x-for="(guest, index) in result.remote_response.your_guest_breakdown" :key="index">
                                        <div class="border border-gray-100 p-3 rounded" :class="{'bg-blue-50': guest.age > 13, 'bg-green-50': guest.age <= 13}">
                                            <p class="text-xs text-gray-500">Guest <span x-text="index + 1"></span></p>
                                            <p class="font-medium text-sm text-gray-900">
                                                <span x-text="guest.age + ' years old'"></span>
                                                <span x-text="guest.age <= 13 ? ' (Child)' : ' (Adult)'" class="text-xs"></span>
                                            </p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Rate Details -->
                            <div class="mb-6" x-show="result.remote_response.Legs && result.remote_response.Legs.length > 0">
                                <h4 class="text-md font-semibold text-gray-900 mb-3">Rate Details</h4>
                                <div class="space-y-3">
                                    <template x-for="(rate, index) in result.remote_response.Legs" :key="index">
                                        <div class="border border-gray-100 p-4 rounded bg-gray-50">
                                            <p class="font-medium text-gray-900 mb-3" x-text="rate['Special Rate Description']"></p>

                                            <div class="grid grid-cols-2 gap-4 mb-3">
                                                <div>
                                                    <p class="text-sm text-gray-500">Daily Rate</p>
                                                    <p class="font-medium text-gray-900" x-text="formatCurrency(rate['Effective Average Daily Rate'])"></p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-500">Total Charge</p>
                                                    <p class="font-medium text-gray-900" x-text="formatCurrency(rate['Total Charge'])"></p>
                                                </div>
                                            </div>

                                            <div class="mt-3" x-show="rate['Deposit Breakdown'] && rate['Deposit Breakdown'].length > 0">
                                                <p class="text-sm font-semibold text-gray-700 mb-2">Deposit Information</p>
                                                <template x-for="(deposit, dIndex) in rate['Deposit Breakdown']" :key="dIndex">
                                                    <div class="flex justify-between items-center text-sm">
                                                        <span x-text="'Due: ' + deposit['Due Date Formatted']"></span>
                                                        <span class="font-medium" x-text="formatCurrency(deposit['Due Amount'])"></span>
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

                // Unit types data
                units: [{
                        name: 'Dessert Whisperer',
                        image: 'https://gondwana-collection.com/hubfs/Gondwana%20Website/02%20TRAVEL%20CENTRE/Lodges%20and%20Camps/Desert%20Whisper/Desert%20Whisper_main%20banner.jpg'
                    },
                    {
                        name: 'Khalahari Camping2Go',
                        image: 'https://gondwana-collection.com/hubfs/Gondwana%20Website/Accommodation/Kalahari%20Anib%20Camping2Go/Kalahari%20Anib%20Camping2Go%20(3).jpg'
                    },
                    // Add more units here
                ],

                // Datepicker specific properties
                showDatepicker: false,
                outputDateFromValue: '', // Displays in the input fields (e.g., "DD/MM/YYYY")
                outputDateToValue: '', // Displays in the input fields
                dateFrom: null, // Date object for selected start date
                dateTo: null, // Date object for selected end date
                currentDate: null,
                endToShow: '', // 'from' or 'to' to indicate which input is active

                month: '',
                year: '',
                no_of_days: [],
                blankdays: [],

                init() {
                    // Initial setup for the date picker based on today's date
                    this.currentDate = new Date();
                    this.month = this.currentDate.getMonth();
                    this.year = this.currentDate.getFullYear();
                    this.getNoOfDays(); // Populate the calendar grid
                    this.updateAgesFields(); // Initial call for occupants

                    // Initialize date picker inputs if form already has values
                    if (this.form.Arrival) {
                        this.dateFrom = this.parseDateForValidation(this.form.Arrival);
                        this.outputDateFromValue = this.form.Arrival;
                    }
                    if (this.form.Departure) {
                        this.dateTo = this.parseDateForValidation(this.form.Departure);
                        this.outputDateToValue = this.form.Departure;
                    }
                },

                // --- Datepicker Methods ---

                // Helper to parse DD/MM/YYYY string into a Date object
                parseDateForValidation(dateString) {
                    if (!dateString) return null;
                    const parts = dateString.split('/');
                    if (parts.length === 3) {
                        // new Date(year, monthIndex, day)
                        const d = new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[
                            0]));
                        d.setHours(0, 0, 0, 0); // Normalize to start of day
                        return d;
                    }
                    return null;
                },

                // Helper to convert DD/MM/YYYY to YYYY-MM-DD for backend
                convertDmYToYmd(dateString) {
                    if (!dateString) return '';
                    const parts = dateString.split('/');
                    if (parts.length === 3) {
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                    return dateString;
                },

                // Helper to convert YYYY-MM-DD (from backend response) to DD/MM/YYYY for display
                formatDateForDisplay(dateYmdString) {
                    if (!dateYmdString) return '';
                    const parts = dateYmdString.split('-');
                    if (parts.length === 3) {
                        return `${parts[2]}/${parts[1]}/${parts[0]}`;
                    }
                    return dateYmdString;
                },

                initDatepicker() {
                    // Determine which month/year to display when opening the picker
                    let targetDate = new Date(); // Default to today

                    if (this.endToShow === 'from' && this.dateFrom) {
                        targetDate = this.dateFrom;
                    } else if (this.endToShow === 'to' && this.dateTo) {
                        targetDate = this.dateTo;
                    } else if (this
                        .dateFrom) { // If dateFrom exists but endToShow isn't set (e.g., re-opening)
                        targetDate = this.dateFrom;
                    } else if (this.dateTo) { // If dateTo exists
                        targetDate = this.dateTo;
                    }

                    this.month = targetDate.getMonth();
                    this.year = targetDate.getFullYear();
                    this.getNoOfDays(); // Re-populate calendar grid for the new month/year
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
                    today.setHours(0, 0, 0, 0); // Normalize today to start of day
                    const d = new Date(this.year, this.month, date);
                    d.setHours(0, 0, 0, 0); // Normalize selected date to start of day
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

                    // If only dateFrom is selected, or if dateTo is being hovered before dateFrom
                    if (!effectiveDateTo || (this.endToShow === 'to' && d < effectiveDateFrom)) {
                        effectiveDateTo = d; // Temporarily consider hovered date as end
                    }

                    // Ensure dateFrom is always before dateTo for range check
                    if (effectiveDateFrom > effectiveDateTo) {
                        [effectiveDateFrom, effectiveDateTo] = [effectiveDateTo, effectiveDateFrom];
                    }

                    return d > effectiveDateFrom && d < effectiveDateTo;
                },

                getDateValue(
                date) { // Removed 'temp' parameter as it's no longer needed for primary logic
                    if (this.isPastDate(date)) {
                        return; // Do nothing if it's a past date
                    }

                    let selectedDate = new Date(this.year, this.month, date);
                    selectedDate.setHours(0, 0, 0, 0); // Normalize to start of day

                    if (!this.dateFrom || (this.dateFrom && this.dateTo)) {
                        // Start a new selection or if both dates are already selected, restart
                        this.dateFrom = selectedDate;
                        this.dateTo = null; // Clear dateTo for new range
                        this.endToShow = 'to'; // Next selection is 'to'
                    } else if (this.dateFrom && !this.dateTo) {
                        // Second click: setting the 'to' date
                        if (selectedDate <= this.dateFrom) {
                            // If selected date is before or same as dateFrom, swap them
                            this.dateTo = this.dateFrom;
                            this.dateFrom = selectedDate;
                        } else {
                            this.dateTo = selectedDate;
                        }
                        this.closeDatepicker(); // Close after selecting both dates
                    }
                    this.outputDateValues(); // Always update input fields for visual feedback
                },

                outputDateValues() {
                    // This updates the input fields and form model after a selection or init
                    if (this.dateFrom) {
                        this.outputDateFromValue = this.dateFrom.toLocaleDateString(
                        'en-GB'); // DD/MM/YYYY
                        this.form.Arrival = this.outputDateFromValue; // Update form value
                    } else {
                        this.outputDateFromValue = '';
                        this.form.Arrival = '';
                    }

                    if (this.dateTo) {
                        this.outputDateToValue = this.dateTo.toLocaleDateString('en-GB'); // DD/MM/YYYY
                        this.form.Departure = this.outputDateToValue; // Update form value
                    } else {
                        this.outputDateToValue = '';
                        this.form.Departure = '';
                    }
                },

                getNoOfDays() {
                    let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
                    let dayOfWeek = new Date(this.year, this.month, 1)
                .getDay(); // Get day of week for 1st of month

                    let blankdaysArray = [];
                    for (let i = 0; i < dayOfWeek; i++) {
                        blankdaysArray.push(i); // Just push dummy values for alignment
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

                // --- Existing Rate Checker Methods (adjusted for new date structure) ---

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
                    // Basic checks for required fields
                    if (!this.form['Unit Name'] || !this.form.Arrival || !this.form.Departure) {
                        return false;
                    }
                    // Date logic: use parseDateForValidation for date objects
                    const arrivalDate = this.parseDateForValidation(this.form.Arrival);
                    const departureDate = this.parseDateForValidation(this.form.Departure);

                    if (!arrivalDate || !departureDate || arrivalDate >= departureDate) {
                        return false;
                    }

                    // Check occupants and ages
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
                        this.error =
                            "Please fill in all required fields and ensure dates are valid.";
                        return;
                    }

                    this.loading = true;

                    // **CHANGE STARTS HERE**
                    // Do NOT convert dates to YYYY-MM-DD if your API expects DD/MM/YYYY.
                    // The `this.form.Arrival` and `this.form.Departure` already hold DD/MM/YYYY.
                    const payload = {
                        'Unit Name': this.form['Unit Name'],
                        'Arrival': this.form.Arrival, // Send as DD/MM/YYYY
                        'Departure': this.form.Departure, // Send as DD/MM/YYYY
                        'Occupants': this.form.Occupants,
                        'Ages': this.form.Ages
                    };
                    // **CHANGE ENDS HERE**
                    console.log('Full Payload sent to API:', payload); // See the exact payload
                    // Define your API endpoint here
                    const apiUrl =
                    'api/get-rates'; // <--- IMPORTANT: Replace with your actual API URL

                    try {
                        const response = await fetch(apiUrl, {
                            method: 'POST', // Assuming your API expects a POST request
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                // Add any other necessary headers
                            },
                            body: JSON.stringify(payload) // Send the payload as JSON
                        });

                        if (!response.ok) {
                            const errorData = await response.json();
                            // Assuming the error data contains an 'errors' object for validation messages
                            if (errorData.errors) {
                                let detailedError = "Validation Errors:<br>";
                                for (const key in errorData.errors) {
                                    detailedError += `- ${errorData.errors[key].join(', ')}<br>`;
                                }
                                throw new Error(detailedError);
                            } else {
                                throw new Error(errorData.message ||
                                    `API error: ${response.statusText}`);
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
                        this.error = e.message ||
                            "Failed to fetch rates. Please check your network connection or the API endpoint.";
                    } finally {
                        this.loading = false;
                    }
                },

                formatCurrency(amount) {
                    // Simple currency formatting for display
                    return new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'NAD'
                    }).format(amount);
                }
            }));
        });
    </script>

    <style>
        /* Custom animations for results/errors */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fadeInDown 0.5s ease-out forwards;
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
        }

        /* Optional: Hide number input spin buttons for a cleaner look */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        /* Alpine x-cloak style */
        [x-cloak] {
            display: none !important;
        }
    </style>
@endsection

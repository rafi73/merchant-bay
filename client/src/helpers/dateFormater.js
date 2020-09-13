const dateFormater = {}

dateFormater.install = function (Vue, options) {

    Vue.prototype.$dateSmall = (value) => {
        return moment(date).format('L')
    }

    Vue.prototype.$dateMedium = (value) => {
        return this.moment(value).format('YYYY-MM-DD')
    }
}
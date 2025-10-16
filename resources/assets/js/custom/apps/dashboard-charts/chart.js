"use strict";

// Class definition
var KTWidgets = function () {
    // Statistics widgets


    var initFlatpickr = () => {
        $(".dr-down a").click(function () {
            let name = $(this).data('name'),
                currentName = $(this).data('current-name'),
                key = $(this).data('key'),
                closeSubMenu = $('#show-hidden-click');
            let currencyId = $('#currency');
            const currency = currencyId.data('currency') ?? '€';
            const direction = currencyId.data('direction') ?? 'end';
            $.ajax({
                url: '/admin/dashboard/states',
                type: 'GET',
                data: {
                    is_duration: key,
                },
                success: function (response) {
                    for (let i = 1; i <= 6; i++) {
                        initChartsWidget1(response, i, currency, direction, name, key);
                    }
                    $('.btn-current').text(currentName ? currentName : "All");
                    closeSubMenu.removeAttr('style').removeClass('show');
                },
            });
        });
    }

    var initChartsWidget1 = function (response, i, currency, direction, lastName, key) {
        let classList = [
            ['sale', 'last_sale', 'percentage_sale', 'total_price_amount', 'last_total_price_amount', 'chart_total_price_amount'],
            ['cost', 'last_cost', 'percentage_cost', 'total_cost_amount', 'last_total_cost_amount', 'chart_total_cost_amount'],
            ['discount', 'last_discount', 'percentage_discount', 'total_discount_amount', 'last_total_discount_amount', 'chart_total_discount_amount'],
            ['profit', 'last_profit', 'percentage_profit', 'total_profit_amount', 'last_total_profit_amount', 'chart_total_profit_amount'],
            ['tax', 'last_tax', 'percentage_tax', 'total_tax_amount', 'last_total_tax_amount', 'chart_total_tax_amount'],
            ['payable', 'last_payable', 'percentage_payable', 'total_payable_amount', 'last_total_payable_amount', 'chart_total_payable_amount']
        ];
        var element = document.getElementById("kt_charts_widget_" + i + "_chart");
        element.innerHTML = ''
        var sale = $("#" + classList[i - 1][0]),
            lastSale = $("#" + classList[i - 1][1]),
            percentageSale = $("#" + classList[i - 1][2]),
            title_for_charts = $("#title-for-js").data('title');
        if (!lastName) {
            lastSale.css('display', 'none')
            percentageSale.css('display', 'none')
        } else {
            lastSale.css('display', '')
            percentageSale.css('display', '')
        }
        let first = response[classList[i - 1][3]];
        let last = response[classList[i - 1][4]];

        if (sale.length) {
            if (direction === 'end') {
                sale.text(parseFloat(first).toFixed(2) + ' ' + currency);
            } else {
                sale.text(currency + ' ' + parseFloat(first).toFixed(2));
            }
        }

        if (lastSale.length) {
            if (direction === 'end') {
                lastSale.html(parseFloat(last).toFixed(2) + ' ' + currency + '<span class="fs-8 ms-1 fw-normal">' + lastName + '</span>');
            } else {
                lastSale.html(currency + ' ' + parseFloat(last).toFixed(2) + '<span class="fs-8 ms-1 fw-normal">' + lastName + '</span>');
            }
        }
        const percentage = last === 0 ? (first !== 0 ? 100 : 0) : (first === 0 ? -100 : (first - last) / last * 100);
        percentageSale.removeAttr("class");
        percentageSale.addClass(percentage <= 0 ? 'badge badge-light-danger' : 'badge badge-light-success')
        percentageSale.text(parseFloat(percentage).toFixed(2) + '%');
        if (!element) {
            return;
        }

        var chart = {
            self: null,
            rendered: false
        };

        var initChart = function () {
            var height = parseInt(KTUtil.css(element, 'height'));
            var labelColor = KTUtil.getCssVariableValue('--bs-gray-500');
            var borderColor = KTUtil.getCssVariableValue('--bs-gray-200');
            var baseColor = KTUtil.getCssVariableValue('--bs-info');
            var lightColor = KTUtil.getCssVariableValue('--bs-info-light');
            const day = [];
            const month = [];
            const year = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            for (let i = 1; i <= 24; i++) {
                day.push(i +"h");
            }
            for (let i = 1; i <= response.days_of_month; i++) {
                month.push(i);
            }
            const chartTotalProfitAmount = response[classList[i - 1][5]];
            const arrayLength = (key === 'month' ? month : (key === 'day' ? day : year)).length;
            const array = Array(arrayLength).fill(0);

            for (const key in chartTotalProfitAmount) {
                const index = parseInt(key) - 1;
                if (index >= 0 && index < arrayLength) {
                    array[index] = parseFloat(chartTotalProfitAmount[key]).toFixed(2);
                }
            }
            var options = {
                series: [{
                    name: title_for_charts[i-1],
                    data: array
                }],
                chart: {
                    fontFamily: 'inherit',
                    type: 'area',
                    height: 200,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {},
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: false
                },
                fill: {
                    type: 'solid',
                    opacity: 1
                },
                stroke: {
                    curve: 'smooth',
                    show: true,
                    width: 3,
                    colors: [baseColor]
                },
                xaxis: {
                    categories: key === 'month' ? month : (key === 'day' ? day : year),
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: labelColor,
                            fontSize: '12px'
                        }
                    },
                    crosshairs: {
                        position: 'front',
                        stroke: {
                            color: baseColor,
                            width: 1,
                            dashArray: 3
                        }
                    },
                    tooltip: {
                        enabled: true,
                        formatter: undefined,
                        offsetY: 0,
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: labelColor,
                            fontSize: '12px'
                        }
                    }
                },
                states: {
                    normal: {
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    },
                    hover: {
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    },
                    active: {
                        allowMultipleDataPointsSelection: false,
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    }
                },
                tooltip: {
                    style: {
                        fontSize: '12px'
                    },
                    y: {
                        formatter: function (val) {
                            return direction === "end" ? val+ " " +currency  : currency+" "+ val
                        }
                    }
                },
                colors: [lightColor],
                grid: {
                    borderColor: borderColor,
                    strokeDashArray: 4,
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                markers: {
                    strokeColor: baseColor,
                    strokeWidth: 3
                }
            };

            chart.self = new ApexCharts(element, options);
            chart.self.render();
            chart.rendered = true;
        }

        // Init chart
        initChart();

        // Update chart on theme mode change
        KTThemeMode.on("kt.thememode.change", function () {
            if (chart.rendered) {
                chart.self.destroy();
            }

            initChart();
        });
    }

    var initChartsWidget7 = function () {
        var element = document.getElementById("kt_charts_widget_7_chart");

        if (!element) {
            return;
        }
        var chart = {
            self: null,
            rendered: false
        };

        var initChart = function () {
            var height = parseInt(KTUtil.css(element, 'height'));
            var labelColor = KTUtil.getCssVariableValue('--bs-gray-500');
            var borderColor = KTUtil.getCssVariableValue('--bs-gray-200');
            var baseColor = KTUtil.getCssVariableValue('--bs-primary');
            var secondaryColor = KTUtil.getCssVariableValue('--bs-gray-300');
            let currencyId = $('#currency');
            const currency = currencyId.data('currency') ?? '€';
            const direction = currencyId.data('direction') ?? 'end';
            let sale = JSON.parse(element.getAttribute('data-value-payable-after-all'))
            let cost = JSON.parse(element.getAttribute('data-value-cost-after-all'))
            for (var i = 0; i < sale.length; i++) {
                sale[i] = parseFloat(sale[i]).toFixed(2);
            }
            for (var j = 0; j < cost.length; j++) {
                cost[j] = parseFloat(cost[j]).toFixed(2);
            }
            var options = {
                series: [{
                    name: element.getAttribute('data-sale'),
                    data: sale
                }, {
                    name: element.getAttribute('data-cost'),
                    data: cost
                }],
                chart: {
                    fontFamily: 'inherit',
                    type: 'bar',
                    height: height,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: ['30%'],
                        borderRadius: [6]
                    },
                },
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: [window.keys.jan, window.keys.feb , window.keys.mar, window.keys.apr, window.keys.may, window.keys.jun, window.keys.jul, window.keys.aug, window.keys.sep, window.keys.oct, window.keys.now, window.keys.dec],
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: labelColor,
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: labelColor,
                            fontSize: '12px'
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                states: {
                    normal: {
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    },
                    hover: {
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    },
                    active: {
                        allowMultipleDataPointsSelection: false,
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    }
                },
                tooltip: {
                    style: {
                        fontSize: '12px'
                    },
                    y: {
                        formatter: function (val) {
                            return direction === "end" ? val+ " " +currency  : currency+" "+ val
                        }
                    }
                },
                colors: [baseColor, secondaryColor],
                grid: {
                    borderColor: borderColor,
                    strokeDashArray: 4,
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                }
            };

            chart.self = new ApexCharts(element, options);
            chart.self.render();
            chart.rendered = true;
        }

        // Init chart
        initChart();

        // Update chart on theme mode change
        KTThemeMode.on("kt.thememode.change", function () {
            if (chart.rendered) {
                chart.self.destroy();
            }

            initChart();
        });
    }
    // Public methods
    return {
        init: function () {
            initChartsWidget7();
            initFlatpickr();
            let currencyId = $('#currency');
            const currency = currencyId.data('currency') ?? '€',
                direction = currencyId.data('direction') ?? 'end',
                lastMonth = $('#last_month').data('name');
            $.ajax({
                url: '/admin/dashboard/states',
                type: 'GET',
                data: {
                    is_duration: 'month'
                },
                success: function (response) {
                    for (let i = 1; i <= 6; i++) {
                        initChartsWidget1(response, i, currency, direction, lastMonth, 'month');
                    }
                },
                error: function (error) {
                    console.error('Error fetching data:', error);
                }
            });
        }
    }
}();

// Webpack support
if (typeof module !== 'undefined' && typeof module.exports !== 'undefined') {
    module.exports = KTWidgets;
}

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTWidgets.init();
});

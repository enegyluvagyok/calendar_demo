<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport">
    <title>Laravel</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>

<body class="antialiased">
    <div style="padding: 5%;">
        <div class="calendar"></div>
    </div>
</body>

</html>

<script src="https://unpkg.com/js-year-calendar@latest/dist/js-year-calendar.min.js"></script>
<script src="https://unpkg.com/js-year-calendar@latest/locales/js-year-calendar.hu.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>


<link rel="stylesheet" type="text/css" href="https://unpkg.com/js-year-calendar@latest/dist/js-year-calendar.min.css" />



<script>
    var dataSource = <?php echo json_encode($events); ?>;

    dataSource.forEach(function(item) {
        item.startDate = new Date(item.startDate);
        item.endDate = new Date(item.endDate);
    });

    const currentYear = new Date().getFullYear();

    function getLastDayOfYear(year) {
        return new Date(year + 1, 11, 31);
    }

    new Calendar('.calendar', {
        style: 'solid',
        language: 'hu',
        weekStart: 1,
        displayWeekNumber: true,
        enableContextMenu: false,
        dataSource: dataSource,
        numberMonthsDisplayed: 12,
        minDate: new Date('2021-12-31'),
        maxDate: getLastDayOfYear(currentYear),
        clickDay: function(e) {
            console.log(e)
            const result = e.date.toLocaleDateString("hu-HU", {
                year: "numeric",
                month: "2-digit",
                day: "2-digit"
            })
            Swal.fire({
                title: 'Nap módosítása:',
                text: result
            })
        },
        dayContextMenu: function(e) {
            console.log(e);
        },
        mouseOnDay: function(e) {
            if (e.events.length == 1) {
               tippy(e.element, {
                    content: e.events[0].note,
               });
            }else{
                tippy(e.element, {
                    content: 'munkanap',
               });
            }
        }
    })
</script>
import os
import sys

def time_rollover(time):
    old_hours = (time//100)
    new_hours = ((time%100) // 60)
    minutes = (time%100) % 60
    total_time = (old_hours * 100) + (new_hours * 100) + minutes
    if old_hours + new_hours >= 24:
        total_time = total_time % 2400
        total_time += 10000
    return total_time

def date_rollover(year, month, day):
    months = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31]
    y = year
    m = month
    d = day
    if y % 4 == 0:
        months[1] = 29
    if d > months[m - 1]:
        d = d % (months[m - 1] + 1) + 1
        m += 1
    if m > 12:
        y += 1
        m = (m % 13) + 1
    date = [y, m, d]
    return date

def parse_time(input_time):
    time_list = input_time.split(':')
    time = 0
    pm = False
    for chunk in time_list:
        for character in chunk:
            if character.isdigit() == False:
                if character.lower() == 'p':
                    pm = True
    for i in range(len(time_list)):
        chunk = time_list[i]
        if len(chunk) <= 2:
            if i == 0:
                time += int(chunk)*100
                if int(chunk) == 12 and pm == False:
                    time -= int(chunk)*100
            else:
                time += int(chunk)
        else:
            numbers = ''
            for character in chunk:
                if character.isdigit() == True:
                    numbers = numbers + character
                else:
                    if character.lower() == 'a':
                        time += 0
                    if character.lower() == 'p':
                        time += 1200
            if len(numbers) <= 2:
                if i == 0:
                    time += int(numbers)*100
                else:
                    time += int(numbers)
            else:
                time += int(numbers)
    return time

def main(name, startyear, startmonth, startday, showstart, showend, weeks):
    os.system('mkdir {}'.format(name))
    year = int(startyear)
    month = int(startmonth)
    day = int(startday)
    start_time = parse_time(showstart)
    end_time = parse_time(showend)
    shows = int(weeks)
    times = []
    for week in range(shows):
        if week > 0:
            day = day + 7
        print('year:', year, 'month:', month, 'day:', day)
        date = date_rollover(year, month, day)
        year = date[0]
        month = date[1]
        day = date[2]
        if end_time < start_time:
            end_time += 2400
        time = start_time
        tmp_day = day
        while time < end_time:
            print(time)
            times.append([month, tmp_day, time])
            time += 30
            time = time_rollover(time)
            if time // 10000 == 1:
                tmp_day += 1
                end_time -= 2400
            time = time % 10000
            print('time:', time, 'end_time:', end_time)
    for t in times:
        os.system('scp krlxdj@garnet.krlx.org:/Volumes/Sapphire/recordings/{0}-{1:02}-{2:02}_{3:04}* ./{4}/'.format(year, t[0], t[1], t[2], name))

if __name__ == '__main__':
    if len(sys.argv) == 1:
        print('Usage: python3 {} showName startYear startMonth startDay showStartTime showEndTime [NumberOfWeeks]'.format(sys.argv[0]))
        quit()
    name = sys.argv[1]
    year = sys.argv[2]
    startmonth = sys.argv[3]
    startday = sys.argv[4]
    showstart = sys.argv[5]
    showend = sys.argv[6]
    if len(sys.argv) == 8:
        shows = sys.argv[7]
    else:
        shows = 1
    if len(sys.argv) > 8:
        print('Usage: python3 sys.argv[0] showName startYear startMonth startDay showStartTime showEndTime [NumberOfWeeks]')
        quit()
    main(name, year, startmonth, startday, showstart, showend, shows)

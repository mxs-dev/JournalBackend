let accessToken = `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOjEsImlhdCI6MTUxNjgwMTAwOSwiaXNzIjoiaHR0cDpcL1wvMTkyLjE2OC4zMy4xMCIsImF1ZCI6Imh0dHA6XC9cLzE5Mi4xNjguMzMuMTAiLCJuYmYiOjE1MTY4MDEwMDksImV4cCI6MTUxNjg4NzQwOSwiZGF0YSI6eyJlbWFpbCI6ImFkbWluQGEuYiIsImxhc3RMb2dpbkF0Ijp7ImV4cHJlc3Npb24iOiJOT1coKSIsInBhcmFtcyI6W119fX0._4-vKfBeRD7NxPmlMikLY_tKV_bu5yBmkpmAG0mY_nc`;

let source = new EventSource(`http://192.168.33.10/v1/sse?access-token=${accessToken}`, {
    withCredentials: true
});

source.addEventListener('open', function(e) {
    console.log('SSE connection is open', e);
}, false);

source.addEventListener('error', function(e) {
    console.log('Error', e);
}, false);


source.addEventListener('message', (event) => {
    console.log(event);
});

console.log(source);
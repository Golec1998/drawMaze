function moveBull()
{
    
    var canv = document.getElementsByTagName('canvas');
    var firstCanv = canv[0];
	
    var canvas = document.createElement('canvas');
    var ctx = canvas.getContext('2d');
    canvas.id = 'canvas_bull';
    canvas.style.position = firstCanv.style.position;
    canvas.style.left = firstCanv.style.left;
    canvas.style.top = firstCanv.style.top;
    canvas.height = firstCanv.offsetHeight;
    canvas.width = firstCanv.offsetWidth;
    
    var victoryCall = document.createElement('div');
    victoryCall.id = 'victoryCall';
    victoryCall.style.position = 'absolute';
    victoryCall.style.left = 0;
    victoryCall.style.top = 0;
    victoryCall.style.display = 'block';
    victoryCall.style.height = 0;
    victoryCall.style.width = '100vw';
    victoryCall.style.overflow = 'hidden';
    victoryCall.style.backgroundColor = 'rgba(0, 0, 0, 0.9)';
    victoryCall.style.color = 'white';
    victoryCall.style.WebkitTransition = 'height 0.3s';
    victoryCall.style.MozTransition = 'height 0.3s';
    
    document.getElementById('mazeIn').appendChild(canvas);
    document.getElementById('mazeIn').appendChild(victoryCall);
    
    var victoryHeader = document.createElement('h1');
    victoryHeader.style.position = 'absolute';
    victoryHeader.style.bottom = '50vh';
    victoryHeader.style.width = '100vw';
    victoryHeader.style.textAlign = 'center';
    document.getElementById('victoryCall').appendChild(victoryHeader);
    victoryHeader.innerHTML = 'Zwycięstwo';
    

    var
    x = parseInt(canvas.width / (maze.width * 2)),
    y = parseInt(canvas.width / (maze.width * 2)),
    dx = 0,
    dy = 0,
    dir = 'none',
    xend = 0,
    yend = 0,
    xfin = 0,
    yfin = 0,
    current = 0,
    ncur = 0,
    ready = true;
    
    var paths = maze.paths;
    console.log(paths);
    
    for(i = 0; i < paths.length; i++)
    {
        if(paths[i].x > xfin)
            xfin = paths[i].x;
        if(paths[i].y > yfin)
            yfin = paths[i].y;
    }
    
    var drawing = setInterval(draw, 10);

    function draw()
    {
        
        if(x == xend)
            dx = 0;
        if(y == yend)
            dy = 0;

        x += dx;
        y += dy;

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        ctx.beginPath();
        ctx.arc(x, y, parseInt(canvas.width / (maze.width * 5)), 0, Math.PI*2, false);
        ctx.fillStyle = 'red';
        ctx.fill();
        ctx.closePath();
        
        
        if(dx == 0 && dy == 0)
        {
            dir = 'none';
            ready = true;
            clearInterval(drawing);
        
            if(x == parseInt((xfin * canvas.width) / maze.width) + parseInt(canvas.width / (maze.width * 2)) && y == parseInt((yfin * canvas.height) / maze.height) + parseInt(canvas.width / (maze.width * 2)))
            {
                ready = false;
                document.getElementById('victoryCall').style.height = '100vh';
            }
        }
    }

    draw();
    
    document.querySelector('#canvas_bull').addEventListener('touchstart', movestart);
    document.querySelector('#canvas_bull').addEventListener('touchmove', moveend);
    document.querySelector('#canvas_bull').addEventListener('touchend', move);

    var newcur = 0;
    var startX = 0, stopX = 0, deltaX = 0, startY = 0, stopY = 0, deltaY = 0;

    function movestart(ev)
    {
        startX = ev.touches[0].screenX;
        startY = ev.touches[0].screenY;
        deltaX = 0;
        deltaY = 0;
    }

    function moveend(ev)
    {
        stopX = ev.touches[0].screenX;
        stopY = ev.touches[0].screenY;
        deltaX = stopX - startX;
        deltaY = stopY - startY;
    }

    function move()
    {
        if(deltaX > 100 && deltaY > -30 && deltaY < 30)
        {
            dir = 'right';
        }
        else if(deltaX < -100 && deltaY > -30 && deltaY < 30)
        {
            dir = 'left';
        }
        else if(deltaY > 100 && deltaX > -30 && deltaX < 30)
        {
            dir = 'down';
        }
        else if(deltaY < -100 && deltaX > -30 && deltaX < 30)
        {
            dir = 'up';
        }
        else
        {
            dir = 'none';
        }
        console.log(dir);
        moveBall();
    }
    
    function moveBall()
    {
        if(dir != 'none' && ready)
        {
            ready = false;
            var arr = paths[current].L;
            
            for(i = 0; i < arr.length; i++)
            {
                switch(dir)
                {
                    case 'right':
                        if (paths[arr[i]].x > paths[current].x && paths[arr[i]].y == paths[current].y)
                        {
                            ncur = arr[i];
                            dx = 1;
                            dy = 0;
                            xend = parseInt((paths[arr[i]].x * canvas.width) / maze.width) + parseInt(canvas.width / (maze.width * 2));
                            yend = y;
                            console.log('(' + x + '[' + paths[current].x + '], ' + y + '[' + paths[current].y + ']) -> (' + xend + '[' + paths[arr[i]].x + '], ' + yend + '[' + paths[arr[i]].y + '])');
                        }
                        break;
                    case 'left':
                        if (paths[arr[i]].x < paths[current].x && paths[arr[i]].y == paths[current].y)
                        {
                            ncur = arr[i];
                            dx = -1;
                            dy = 0;
                            xend = parseInt((paths[arr[i]].x * canvas.width) / maze.width) + parseInt(canvas.width / (maze.width * 2));
                            yend = y;
                            console.log('(' + x + '[' + paths[current].x + '], ' + y + '[' + paths[current].y + ']) -> (' + xend + '[' + paths[arr[i]].x + '], ' + yend + '[' + paths[arr[i]].y + '])');
                        }
                        break;
                    case 'down':
                        if (paths[arr[i]].y > paths[current].y && paths[arr[i]].x == paths[current].x)
                        {
                            ncur = arr[i];
                            dx = 0;
                            dy = 1;
                            xend = x;
                            yend = parseInt((paths[arr[i]].y * canvas.height) / maze.height) + parseInt(canvas.width / (maze.width * 2));
                            console.log('(' + x + '[' + paths[current].x + '], ' + y + '[' + paths[current].y + ']) -> (' + xend + '[' + paths[arr[i]].x + '], ' + yend + '[' + paths[arr[i]].y + '])');
                        }
                        break;
                    case 'up':
                        if (paths[arr[i]].y < paths[current].y && paths[arr[i]].x == paths[current].x)
                        {
                            ncur = arr[i];
                            dx = 0;
                            dy = -1;
                            xend = x;
                            yend = parseInt((paths[arr[i]].y * canvas.height) / maze.height) + parseInt(canvas.width / (maze.width * 2));
                            console.log('(' + x + '[' + paths[current].x + '], ' + y + '[' + paths[current].y + ']) -> (' + xend + '[' + paths[arr[i]].x + '], ' + yend + '[' + paths[arr[i]].y + '])');
                        }
                        break;
                }
            }
            drawing = setInterval(draw, 1);
            current = ncur;
        }
    }
	
}
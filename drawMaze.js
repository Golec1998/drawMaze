function drawMaze()
{
var canvas = document.createElement('canvas');
canvas.style.position = "absolute";
canvas.style.top=0;
canvas.style.left=0;
canvas.id="maze";
canvas.height = screen.height;
canvas.width = screen.width;

var Path = maze.paths;
ctx = canvas.getContext("2d");
ctx.lineCap = "round";
ctx.lineJoin = "round";
ctx.lineWidth = canvas.width / (maze.width * 2);

for(let i=0; i<Path.length; i++){
	let x1=Path[i].x;
	let y1=Path[i].y;
	for(let j=0; j<Path[i].L.length; j++){
		let x2=Path[Path[i].L[j]].x;
		let y2=Path[Path[i].L[j]].y;
		
		ctx.beginPath();
		ctx.moveTo((x1 * canvas.width) / maze.width + ctx.lineWidth  , (y1 * canvas.height) / maze.height + ctx.lineWidth);
        ctx.lineTo((x2 * canvas.width) / maze.width + ctx.lineWidth , (y2 * canvas.height) / maze.height + ctx.lineWidth);
		ctx.strokeStyle = '#87CEFA';
        ctx.stroke();
        ctx.closePath();
	}
}

document.querySelector('#mazeIn').appendChild(canvas);
}
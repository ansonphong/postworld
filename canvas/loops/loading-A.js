var lib, images, createjs;

(function (lib, img, cjs) {

var p; // shortcut to reference prototypes

// library properties:
lib.properties = {
	width: 160,
	height: 160,
	fps: 24,
	//color: "#CCCCCC",
	manifest: []
};

// stage content:
(lib.loading_A = function() {
	this.initialize();

	// Layer 1
	this.instance = new lib.rock02();
	this.instance.setTransform(90.3,76,1,1,0,0,0,10.3,-4);

	this.addChild(this.instance);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(118.9,108.5,103,94.9);


// symbols:
(lib._18 = function() {
	this.initialize();

	// Layer 1
	this.shape = new cjs.Shape();
	this.shape.graphics.f().s("#000000").ss(3,1,1).p("AiqhFQDGACCPCJ");
	this.shape.setTransform(17.6,-43);

	this.addChild(this.shape);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(-1,-51.5,37.2,17);


(lib._14 = function() {
	this.initialize();

	// Layer 1
	this.shape = new cjs.Shape();
	this.shape.graphics.f().s("#000000").ss(3,1,1).p("Aj2j5QDMACCPCRQCSCQAADQ");
	this.shape.setTransform(25.3,-25);

	this.addChild(this.shape);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(-1,-51.5,52.5,53);


(lib._12 = function() {
	this.initialize();

	// Layer 1
	this.shape = new cjs.Shape();
	this.shape.graphics.f().s("#000000").ss(3,1,1).p("Aj1nzQDMACCPCRQCSCSAADOQAADPiSCSQiFCHi7ALQgNAAgOABQgBAAgBAA");
	this.shape.setTransform(25.1,0);

	this.addChild(this.shape);
}).prototype = p = new cjs.Container();
p.nominalBounds = new cjs.Rectangle(-1.2,-51.5,52.8,103);


(lib.rock02 = function(mode,startPosition,loop) {
	this.initialize(mode,startPosition,loop,{});

	// timeline functions:
	this.frame_0 = function() {
		/* if (booot != 1){
			this.gotoAndPlay( Math.round(Math.random()*this._totalframes) )
			
			booot = 1
		
		}*/
	}

	// actions tween:
	this.timeline.addTween(cjs.Tween.get(this).call(this.frame_0).wait(210));

	// Layer 4
	this.instance = new lib._18();
	this.instance.setTransform(0.1,0,0.6,0.6,135);

	this.timeline.addTween(cjs.Tween.get(this.instance).to({rotation:270},19,cjs.Ease.get(1)).to({scaleX:0.8,scaleY:0.8},10,cjs.Ease.get(1)).to({rotation:135},20,cjs.Ease.get(1)).to({scaleX:1.2,scaleY:1.2},10,cjs.Ease.get(1)).to({rotation:-45,x:0.2,y:0.1},20,cjs.Ease.get(1)).to({rotation:-135},10,cjs.Ease.get(1)).to({rotation:0},20,cjs.Ease.get(1)).to({scaleX:1,scaleY:1},10,cjs.Ease.get(1)).to({rotation:-90},20,cjs.Ease.get(1)).to({scaleX:1.2,scaleY:1.2},10,cjs.Ease.get(1)).to({rotation:-45},20,cjs.Ease.get(1)).to({scaleX:0.8,scaleY:0.8},10,cjs.Ease.get(1)).to({rotation:135,x:0.1,y:0},20,cjs.Ease.get(1)).to({scaleX:0.6,scaleY:0.6},10,cjs.Ease.get(1)).wait(1));

	// Layer 1
	this.instance_1 = new lib._12();
	this.instance_1.setTransform(0.1,0,0.8,0.8,180);

	this.timeline.addTween(cjs.Tween.get(this.instance_1).to({rotation:45},19,cjs.Ease.get(1)).to({rotation:-45},10,cjs.Ease.get(1)).to({rotation:-180},20,cjs.Ease.get(1)).to({scaleX:1.2,scaleY:1.2},10,cjs.Ease.get(1)).to({rotation:0,x:0.2,y:0.1},20,cjs.Ease.get(1)).to({scaleX:1,scaleY:1},10,cjs.Ease.get(1)).to({rotation:-135},20,cjs.Ease.get(1)).to({scaleX:0.8,scaleY:0.8,x:0,y:0},10,cjs.Ease.get(1)).to({rotation:0,x:0.2,y:0.4},20,cjs.Ease.get(1)).to({scaleX:0.6,scaleY:0.6},10,cjs.Ease.get(1)).to({rotation:135},20,cjs.Ease.get(1)).to({rotation:90},10,cjs.Ease.get(1)).to({rotation:180,x:0.1,y:0},20,cjs.Ease.get(1)).to({scaleX:0.8,scaleY:0.8},10,cjs.Ease.get(1)).wait(1));

	// Layer 3
	this.instance_2 = new lib._18();
	this.instance_2.setTransform(0.1,0,1.2,1.2,90);

	this.timeline.addTween(cjs.Tween.get(this.instance_2).to({rotation:-45},19,cjs.Ease.get(1)).to({scaleX:1,scaleY:1},10,cjs.Ease.get(1)).to({rotation:90},20,cjs.Ease.get(1)).to({scaleX:0.8,scaleY:0.8},10,cjs.Ease.get(1)).to({rotation:-45,y:-0.1},20,cjs.Ease.get(1)).to({scaleX:1,scaleY:1},10,cjs.Ease.get(1)).to({rotation:-180,y:0.3},20,cjs.Ease.get(1)).to({scaleX:1.2,scaleY:1.2},10,cjs.Ease.get(1)).to({rotation:-225},20,cjs.Ease.get(1)).to({scaleX:0.8,scaleY:0.8},10,cjs.Ease.get(1)).to({rotation:-315,x:0.4},20,cjs.Ease.get(1)).to({scaleX:1,scaleY:1,x:0.5,y:0.1},10,cjs.Ease.get(1)).to({rotation:-270,x:0.1,y:0},20,cjs.Ease.get(1)).to({scaleX:1.2,scaleY:1.2},10,cjs.Ease.get(1)).wait(1));

	// Layer 2
	this.instance_3 = new lib._14();

	this.timeline.addTween(cjs.Tween.get(this.instance_3).to({rotation:135},19,cjs.Ease.get(1)).to({rotation:225},10,cjs.Ease.get(1)).to({rotation:360,x:0.1,y:-0.1},20,cjs.Ease.get(1)).to({scaleX:0.8,scaleY:0.8},10,cjs.Ease.get(1)).to({rotation:225,y:0},20,cjs.Ease.get(1)).to({scaleX:0.6,scaleY:0.6},10,cjs.Ease.get(1)).to({rotation:45},20,cjs.Ease.get(1)).to({scaleX:0.8,scaleY:0.8},10,cjs.Ease.get(1)).to({rotation:180,x:0.3,y:0.5},20,cjs.Ease.get(1)).to({scaleX:1,scaleY:1},10,cjs.Ease.get(1)).to({rotation:90,x:0.6,y:0.1},20,cjs.Ease.get(1)).to({scaleX:1.2,scaleY:1.2,x:0.4,y:-0.2},10,cjs.Ease.get(1)).to({rotation:0,x:0,y:0},20,cjs.Ease.get(1)).to({scaleX:1,scaleY:1},10,cjs.Ease.get(1)).wait(1));

}).prototype = p = new cjs.MovieClip();
p.nominalBounds = new cjs.Rectangle(-41.1,-51.5,103,94.9);

})(lib = lib||{}, images = images||{}, createjs = createjs||{});

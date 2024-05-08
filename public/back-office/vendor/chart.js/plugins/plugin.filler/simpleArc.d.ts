export class simpleArc {
    x: any;
    y: any;
    radius: any;

    constructor(opts: any);

    pathSegment(ctx: any, bounds: any, opts: any): boolean;

    interpolate(point: any): {
        x: any;
        y: any;
        angle: any;
    };
}

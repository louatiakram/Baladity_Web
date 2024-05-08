export default class Animation {
    _active: boolean;
    _fn: any;
    _easing: any;
    _start: number;
    _duration: number;
    _total: number;
    _loop: boolean;
    _target: any;
    _prop: any;
    _from: unknown;
    _to: any;
    _promises: any[];

    constructor(cfg: any, target: any, prop: any, to: any);

    active(): boolean;

    update(cfg: any, to: any, date: any): void;

    cancel(): void;

    tick(date: any): void;

    wait(): Promise<any>;

    _notify(resolved: any): void;
}

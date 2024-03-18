export default class BubbleController extends DatasetController {
    static id: string;
    /**
     * @type {any}
     */
    static overrides: any;

    update(mode: any): void;

    /**
     * Parse array of primitive values
     * @protected
     */
    protected parsePrimitiveData(meta: any, data: any, start: any, count: any): any;

    /**
     * Parse array of arrays
     * @protected
     */
    protected parseArrayData(meta: any, data: any, start: any, count: any): any;

    /**
     * Parse array of objects
     * @protected
     */
    protected parseObjectData(meta: any, data: any, start: any, count: any): any;

    /**
     * @protected
     */
    protected getMaxOverflow(): number;

    /**
     * @protected
     */
    protected getLabelAndValue(index: any): {
        label: any;
        value: string;
    };
}
import DatasetController from "../core/core.datasetController.js";

export default class RadarController extends DatasetController {
    static id: string;
    /**
     * @type {any}
     */
    static overrides: any;

    parseObjectData(meta: any, data: any, start: any, count: any): {
        r: unknown;
    }[];

    update(mode: any): void;

    /**
     * @protected
     */
    protected getLabelAndValue(index: any): {
        label: any;
        value: string;
    };
}
import DatasetController from "../core/core.datasetController.js";

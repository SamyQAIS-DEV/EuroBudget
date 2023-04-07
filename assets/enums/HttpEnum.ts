export enum HttpResponseCodeEnum {
    HTTP_OK = 200,
    HTTP_NO_CONTENT = 204,
    HTTP_MOVED_PERMANENTLY = 301,
    HTTP_FORBIDDEN = 403,
    HTTP_NOT_FOUND = 404,
    HTTP_UNPROCESSABLE_ENTITY = 422,
    HTTP_SERVER_ERROR = 500,
}

export enum HttpRequestMethodEnum {
    GET = 'GET',
    POST = 'POST',
    PUT = 'PUT',
    PATCH = 'PATCH',
    DELETE = 'DELETE',
}
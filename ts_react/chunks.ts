export enum CHUNK_METHOD {
  CONTIGUOUS, PRIORITIZE_FIRST, PRIORITIZE_CENTER, PRIORITIZE_LAST
}

export const evenChunks = <T>(input: Array<T>, n = 1, method: CHUNK_METHOD = 0): T[][] => {
  const ret = Array(+n);
  let i, j = 0;

  const overflow = input.length % n;
  const smallSliceLength = Math.floor(input.length / n);

  switch (method) {
    default:
    case CHUNK_METHOD.CONTIGUOUS:
      for (i = 0; i < ret.length; i++) {
        const sliceEnd = Math.round((i + 1) * (input.length / n));
        ret[i] = input.slice(j, sliceEnd);
        j = sliceEnd;
      }
      break;
    case CHUNK_METHOD.PRIORITIZE_FIRST:
      for (i = 0; i < ret.length; i++) {
        const sliceEnd = j + smallSliceLength + Number(i < overflow);
        ret[i] = input.slice(j, sliceEnd);
        j = sliceEnd;
      }
      break;
    case CHUNK_METHOD.PRIORITIZE_CENTER:
      for (i = 0; i < ret.length; i++) {
        const sliceEnd = j + smallSliceLength + Number(i >= (n - overflow) / 2 && i < n - (n - overflow) / 2);
        ret[i] = input.slice(j, sliceEnd);
        j = sliceEnd;
      }
      break;
    case CHUNK_METHOD.PRIORITIZE_LAST:
      for (i = 0; i < ret.length; i++) {
        const sliceEnd = j + smallSliceLength + Number(i >= n - overflow);
        ret[i] = input.slice(j, sliceEnd);
        j = sliceEnd;
      }
      break;

  }

  return ret;
}

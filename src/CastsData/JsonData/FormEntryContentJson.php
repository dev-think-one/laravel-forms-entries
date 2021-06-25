<?php


namespace FormEntries\CastsData\JsonData;

use Illuminate\Http\Request;

class FormEntryContentJson extends AbstractJsonData
{
    protected string $subjectKey = 'subject';

    protected array $requestKeys = ['subject', 'message'];

    /**
     * @param Request $request
     *
     * @return self
     */
    public function fromRequest(Request $request): self
    {
        $this->data = $request->only($this->getRequestKeys());

        return $this;
    }

    public function getSubjectKey(): string
    {
        return $this->subjectKey;
    }

    public function getRequestKeys(): array
    {
        return $this->requestKeys;
    }

    public function subject(): string
    {
        return $this->getAttribute($this->getSubjectKey(), '- No subject -');
    }

    public function stringify(): string
    {
        return $this->subject();
    }
}

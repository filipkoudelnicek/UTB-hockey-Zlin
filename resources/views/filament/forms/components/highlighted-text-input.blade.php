<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            state: $wire.entangle('{{ $getStatePath() }}'),
            syncing: false,
            init() {
                this.renderState()

                this.$watch('state', () => {
                    if (! this.syncing && document.activeElement !== this.$refs.editor) {
                        this.renderState()
                    }
                })
            },
            escape(value) {
                const node = document.createElement('span')
                node.textContent = value ?? ''

                return node.innerHTML
            },
            renderState() {
                const source = String(this.state ?? '')
                const pattern = /<span data-highlight=(?:&quot;|')accent(?:&quot;|')>([\s\S]*?)<\/span>/g
                const fragment = document.createDocumentFragment()
                let lastIndex = 0
                let match

                while ((match = pattern.exec(source)) !== null) {
                    fragment.append(document.createTextNode(source.slice(lastIndex, match.index)))

                    const highlight = document.createElement('span')
                    highlight.dataset.highlight = 'accent'
                    highlight.style.color = '#f47800'
                    highlight.textContent = match[1]
                    fragment.append(highlight)

                    lastIndex = pattern.lastIndex
                }

                fragment.append(document.createTextNode(source.slice(lastIndex)))
                this.$refs.editor.replaceChildren(fragment)
            },
            serialize(node) {
                return Array.from(node.childNodes).map((child) => {
                    if (child.nodeType === Node.TEXT_NODE) {
                        return child.textContent ?? ''
                    }

                    const content = this.serialize(child)

                    return child.nodeName === 'SPAN' && child.dataset.highlight === 'accent'
                        ? '<span data-highlight=&quot;accent&quot;>' + content + '</span>'
                        : content
                }).join('')
            },
            sync() {
                this.syncing = true
                this.state = this.serialize(this.$refs.editor)
                this.$refs.state.value = this.state
                this.$refs.state.dispatchEvent(new Event('input', { bubbles: true }))
                this.$nextTick(() => this.syncing = false)
            },
            isHighlightedSelection(selection) {
                const range = selection.getRangeAt(0)
                const elementFor = (node) => node.nodeType === Node.ELEMENT_NODE ? node : node.parentElement
                const startHighlight = elementFor(range.startContainer)?.closest('[data-highlight=&quot;accent&quot;]')
                const endHighlight = elementFor(range.endContainer)?.closest('[data-highlight=&quot;accent&quot;]')

                return startHighlight && startHighlight === endHighlight
            },
            removeHighlight(selection) {
                const range = selection.getRangeAt(0)
                const elementFor = (node) => node.nodeType === Node.ELEMENT_NODE ? node : node.parentElement
                const highlight = elementFor(range.startContainer)?.closest('[data-highlight=&quot;accent&quot;]')

                if (! highlight) {
                    return
                }

                const beforeRange = document.createRange()
                beforeRange.selectNodeContents(highlight)
                beforeRange.setEnd(range.startContainer, range.startOffset)

                const afterRange = document.createRange()
                afterRange.selectNodeContents(highlight)
                afterRange.setStart(range.endContainer, range.endOffset)

                const fragment = document.createDocumentFragment()
                const appendHighlight = (text) => {
                    if (! text) {
                        return
                    }

                    const part = document.createElement('span')
                    part.dataset.highlight = 'accent'
                    part.style.color = '#f47800'
                    part.textContent = text
                    fragment.append(part)
                }

                appendHighlight(beforeRange.toString())
                fragment.append(document.createTextNode(range.toString()))
                appendHighlight(afterRange.toString())
                highlight.replaceWith(fragment)
            },
            applyHighlightStyles() {
                this.$refs.editor.querySelectorAll('[data-highlight=&quot;accent&quot;]').forEach((highlight) => {
                    highlight.style.color = '#f47800'
                })
            },
            highlight() {
                const selection = window.getSelection()

                if (! selection || selection.isCollapsed || ! this.$refs.editor.contains(selection.anchorNode) || ! this.$refs.editor.contains(selection.focusNode)) {
                    return
                }

                if (this.isHighlightedSelection(selection)) {
                    this.removeHighlight(selection)
                } else {
                    document.execCommand(
                        'insertHTML',
                        false,
                        '<span data-highlight=&quot;accent&quot; style=&quot;color:#f47800&quot;>' + this.escape(selection.toString()) + '</span>',
                    )
                }

                this.applyHighlightStyles()
                this.sync()
            },
        }"
        class="fi-input-wrp {{ $isDisabled() ? 'fi-disabled' : '' }}"
    >
        <input
            x-ref="state"
            type="hidden"
            {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
            value="{{ $getState() }}"
        />

        <div class="fi-input-wrp-content-ctn">
            <div
                x-ref="editor"
                wire:ignore
                contenteditable="{{ $isDisabled() ? 'false' : 'true' }}"
                role="textbox"
                aria-multiline="false"
                x-on:input="sync()"
                x-on:keydown.enter.prevent
                class="fi-input block min-h-10 w-full px-3 py-2 text-sm leading-6 outline-none {{ $isDisabled() ? 'cursor-not-allowed opacity-70' : '' }}"
            ></div>
        </div>

        @if (! $isDisabled())
            <div class="fi-input-wrp-suffix fi-inline">
                <button
                    type="button"
                    x-on:mousedown.prevent
                    x-on:click="highlight()"
                    class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md text-white shadow-sm transition hover:brightness-110 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
                    style="background-color:#f47800; outline-color:#f47800"
                    title="Zvýraznit označený text oranžově"
                    aria-label="Zvýraznit označený text oranžově"
                >
                    <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                        <path d="m14.7 6.3 3 3"></path>
                        <path d="m4 20 4.2-1 9.5-9.5a2.1 2.1 0 0 0-3-3L5.2 16Z"></path>
                        <path d="M4 20h16"></path>
                    </svg>
                </button>
            </div>
        @endif
    </div>
</x-dynamic-component>
